<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . 'libraries/Google/Google_base.php';

/**
 * Google Analytics Library
 *
 * ISO Language code reference
 * https://ja.wikipedia.org/wiki/ISO_639-1%E3%82%B3%E3%83%BC%E3%83%89%E4%B8%80%E8%A6%A7
 */
class Google_Translate extends Google_base
{
    /**
     * @var Google_Service_Translate
     */
    protected $service = null;

    /**
     * Google_Translate constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);

        $this->authorize();
        $this->service = $this->getInstance('Google_Service_Translate');
    }

    /**
     * @param $str
     * @param string $to
     * @param null $from
     *
     * @param array $options
     * @return string
     */
    public function translate($str, $to = 'en', $from = null, $options = [])
    {
        mb_regex_encoding('utf-8');
        mb_internal_encoding('utf-8');

        // Find delimiter
        if (preg_match('/。|！|？/', $str)) {
            $search = '/。|！|？/';
            $delimiter = "。";
        } else {
            $search = '/\./';
            $delimiter = ". ";
        }

        if (!$str) {
            return "";
        }

        if (mb_strlen($str) >= 900) {

            $lines = preg_split("/\n\n/", $str);
            $tmp = [];
            foreach ($lines AS $line) {
                switch (TRUE) {
                    // Split again
                    case mb_strlen($line) >= 900:
                        $sub_lines = preg_split($search, $line);

                        $combines = [];
                        $s = "";
                        foreach ($sub_lines AS $sub_line) {

                            if (empty($sub_line)) {
                                continue;
                            }

                            if ((mb_strlen($s) + mb_strlen($sub_line)) >= 900) {
                                $combines[] = $s;
                                $s = "";
                            }

                            $s .= trim($sub_line) . $delimiter;
                        }
                        $combines[] = $s;

                        $sub_str = "";
                        foreach ($combines AS $combine) {
                            $res = $this->service->translations->listTranslations($combine, $to,
                                array_merge([
                                    'format' => 'text'
                                ], $options)
                            );

                            $sub_str .= $res->data['translations'][0]["translatedText"] . " ";
                        }
                        unset($combines, $combine, $sub_lines, $sub_line);

                        $tmp[] = trim($sub_str);
                        break;

                    // Do translate
                    default:
                        $res = $this->service->translations->listTranslations($line, $to,
                            array_merge([
                                'format' => 'text'
                            ], $options)
                        );

                        $tmp[] = $res->data['translations'][0]["translatedText"];
                        break;
                }
            }

            return implode("\n\n", $tmp);
        }

        $res = $this->service->translations->listTranslations($str, $to, array_merge([
            'format' => 'text'
        ], $options));

        return $res->data['translations'][0]["translatedText"];
    }

    /**
     * Get language list
     *
     * if you put target params in options you can get language list from target language
     *
     * @param array $options
     */
    public function get_language($options = [])
    {
        $res = $this->service->languages->listLanguages($options);
        return $res->data['languages'];

    }
}
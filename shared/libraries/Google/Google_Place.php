<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . 'libraries/Google/Google_base.php';

/**
 * Google Place web service accessing Library
 */
class Google_Place extends Google_base
{
    private $api_url = 'https://maps.googleapis.com/maps/api';

    /**
     * Google_Search constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->authorize();
    }

    /**
     * @param $params
     * @return mixed
     */
    public function search_by_keyword($params)
    {
        $url = $this->api_url . '/place/textsearch/json';
        $params = array_merge([
            'language' => 'ja'
        ], $params, [
            'key' => $this->config['api_key']
        ]);

        $res = file_get_contents($url . '?' . http_build_query($params));
        return json_decode($res, TRUE);
    }

    /**
     * @param string $place_id Place ID from google
     * @param string $lang Language
     * @return mixed
     */
    public function get_detail($place_id, $lang = 'ja')
    {
        $url = $this->api_url . '/place/details/json';
        $params = array_merge([
            'placeid' => $place_id,
            'language' => $lang,
            'key' => $this->config['api_key']
        ]);

        $res = file_get_contents($url . '?' . http_build_query($params));
        return json_decode($res, TRUE);
    }

    /**
     * @param array $params
     * @return string
     */
    public function get_static_map($params = [])
    {
        if (!empty($params['markers'])) {
            $arr = [];
            foreach ($params['markers'] AS $k => $v) {
                switch ($k) {
                    case 'geometry':
                        $arr[] = $v;
                        break;
                    default:
                        $arr[] = sprintf('%s:%s', $k, $v);
                        break;
                }
            }

            $params['markers'] = implode('|', $arr);;
        }

        $url = $this->api_url . "/staticmap";
        $params = array_merge([
            'size' => '640x640',
            'language' => 'ja',
            'key' => $this->config['api_key']
        ], $params);

       return file_get_contents($url . '?' . http_build_query($params));

    }
}

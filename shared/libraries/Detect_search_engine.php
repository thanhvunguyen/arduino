<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 検索エンジンクラス
 */
class Detect_Search_Engine
{
    private $refer = null;
    private $search_engine = [];

    private $needle = [];
    private $compile = [];

    public function __construct() {

        if (isset($_SERVER['HTTP_REFERER']))
        {
            $this->refer = trim($_SERVER['HTTP_REFERER']);
        }

        if ( ! is_null($this->refer))
        {
            if ($this->_load_engine_file())
            {
                $this->_compile_data();
            }
        }
    }

    /**
     * リファラをセット
     *
     * @param null $refer
     */
    public function set_referrer($refer = null)
    {
        $this->refer = $refer;
        $this->compile = [];

        if (empty($this->search_engine)) {
            $this->_load_engine_file();
        }

        $this->_compile_data();
    }

    /**
     * データのコンパイル
     *
     * @return bool;
     */
    private function _compile_data()
    {
        $refer = $this->refer;
        $this->compile = [];
        $keyword = null;

        if (!$refer) {
            $this->compile = [
                'type' => 'direct',
                'name' => null,
                'host' => null,
                'keyword' => null
            ];
            return TRUE;
        }

        $domain = parse_url($refer);

        $target = FALSE;
        foreach ($this->search_engine AS $v) {
            if (preg_match('/'.$v['needle'].'/', $domain['host'])) {
                $target = $v;
                break;
            }
        }

        //ターゲットが見つからない場合
        if (!$target) {
            $this->compile = [
                'type' => 'refer',
                'name' => null,
                'host' => $domain['host'],
                'keyword' => null
            ];
            return TRUE;
        }

        //SNSの場合
        if ($target['type'] == 'sns') {
            $this->compile = [
                'type' => 'sns',
                'name' => $target['name'],

                'host' => $domain['host'],
                'keyword' => null
            ];
            return TRUE;
        }

        //queryのパース
        $q = $this->parse_url($domain['query']);
        foreach ($q AS $k => $v) {
            if (strtolower($k) == $target['query']) {
                $keyword = $v;
                break;
            }
        }

        $this->compile = [
            'type' => 'search',
            'name' => $target['name'],

            'host' => $domain['host'],
            'keyword' => $keyword
        ];

        return TRUE;
    }

    /**
     * URLパラメータを分解
     *
     * @param $query
     * @return array
     */
    private function parse_url($query)
    {
        $params = [];

        mb_language('japanese');
        mb_internal_encoding('UTF-8');

        $q = mb_split('&', $query);

        foreach ($q AS $v) {
            $p = mb_split('=',$v);
            $params[trim($p[0])] = trim(mb_convert_encoding(urldecode($p[1]), 'utf-8', 'auto'));
        }

        return $params;
    }

    public function get_data()
    {
        return $this->compile;
    }

    /**
     * 検索エンジン種別をロード
     */
    private function _load_engine_file()
    {
        $this->search_engine = [];
        $file = SHAREDPATH . "config/search_engine.csv";

        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (!$data[0]) {
                    continue;
                }

                $this->search_engine[] = [
                    'name' => $data[0],
                    'domain' => $data[1],
                    'needle' => $data[2],
                    'query' => strtolower($data[3]),

                    'type' => !empty($data[4]) ? $data[4] : 'search_engine'
                ];

                unset($data);
            }
            fclose($handle);

            return TRUE;
        }

        log_message('error', 'search_engine file is not found.');
        return FALSE;
    }

}

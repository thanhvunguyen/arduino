<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (! defined('MAX_FILE_SIZE')) define('MAX_FILE_SIZE', 6000000);
use Sunra\PhpSimple\HtmlDomParser;

/**
 * Amp_api
 * @property APP_Loader load
 * @property APP_Config config
 * @property Cache_amp_image_model cache_amp_image_model
 */
class Amp_api {

    /**
     * @var object
     */
    private $CI = null;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('cache_amp_image_model');
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function set_image_size($html)
    {
        /** @var simple_html_dom $dom */
        $dom = HtmlDomParser::str_get_html($html);
        $img = $dom->find('img');

        if (count($img) == 0) {
            return $html;
        }

        $ids = array_filter(array_map(function ($v) {
            /** @var simple_html_dom_node $v */
            $tmp = $v->getAllAttributes();

            if (empty($tmp['width']) || empty($tmp['height'])) {
                return md5(preg_replace('/^\/\//', 'http://', $tmp['src']));
            }

            return FALSE;
        }, $img));

        $cache = array();
        if (!empty($ids)) {
            $res = $this->CI->cache_amp_image_model
                ->select('hash, width, height')
                ->where_in('hash', $ids)
                ->all();

            foreach ($res AS $v) {
                $cache[$v->hash] = $v;
            }
            unset($res);
        }

        /** @var simple_html_dom_node $v */
        foreach($dom->find('img') AS $v) {
            $tmp = $v->getAllAttributes();

            /** @var simple_html_dom_node $v */
            if (empty($tmp['width']) || empty($tmp['height'])) {
                $url = preg_replace('/^\/\//', 'http://', $tmp['src']);
                $md5 = md5($url);

                if (!empty($cache[$md5])) {
                    $v->setAttribute('width', $cache[$md5]->width);
                    $v->setAttribute('height', $cache[$md5]->height);
                    continue;
                }

                $path = $this->_get_file($url);
                $size = @getimagesize($path);
                unlink($path);

                if (!empty($size[0]) && !empty($size[1])) {
                    $v->setAttribute('width', $size[0]);
                    $v->setAttribute('height', $size[1]);

                    $this->CI->cache_amp_image_model->set_cache([
                        'url' => $url,
                        'width' => $size[0],
                        'height' => $size[1]
                    ]);
                }

                unset($size);
            }
        }

        $html = $dom->save();
        $dom->clear();
        unset($dom);

        return $html;
    }

    private function _get_file($url)
    {
        set_time_limit(0);
        $path = sys_get_temp_dir() . '/' . md5($url);

        //This is the file where we save the information
        $fp = fopen($path, 'w+');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return $path;
    }

    /**
     * @param string $html
     * @return string
     */
    public function convert($html)
    {
        /** @var simple_html_dom $dom */
        $dom = HtmlDomParser::str_get_html($html);
        
        /** @var simple_html_dom_node $v */
        foreach($dom->find('img') AS $v) {
            $tmp = $v->getAllAttributes();
            $tmp['width'] = !empty($tmp['width']) ? $tmp['width'] : 300;
            $tmp['height'] = !empty($tmp['height']) ? $tmp['height'] : 300;

            /** @var simple_html_dom_node $pl */
            $pl = $v->parent()->parent();
            $class = null;
            if ($pl) {
                $class = $pl->getAttribute('class');
            }

            switch (TRUE) {
                case preg_match('/md\-img\-right\-fix/', $class):
                case preg_match('/md\-img\-left\-fix/', $class):
                    $p = 150 / $tmp['width'];

                    $tmp['layout'] = 'fixed';
                    $tmp['class'] = 'md-logo';
                    $tmp['width'] = $p > 1 ? $tmp['width'] : 150;
                    $tmp['height'] = $p > 1 ? $tmp['height'] : $tmp['height'] * $p;
                    break;

                default:
                    $tmp['layout'] = 'responsive';
                    $tmp['class'] = 'md-photo';
                    break;

                case $tmp['width'] <= 150:
                    $tmp['layout'] = 'fixed';
                    $tmp['class'] = 'md-logo';

                    $pl->setAttribute('class', 'md-img-right-fix');
                    break;
            }

            $attr = [];
            foreach ($tmp AS $name => $value) {
                $attr[] = sprintf('%s="%s"', $name, $value);
            }

            $v->outertext = sprintf('<amp-img %s></amp-img>', implode(' ', $attr));
        }

        $html = $dom->save();
        $dom->clear();
        unset($dom);

        return $html;
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class External_api
 *
 * @property cache_external_api_model cache_external_api_model
 * @property object CI
 */
trait External_api
{
    protected $CI = null;
    protected $api_base = null;

    /**
     * @param $url
     * @return bool|mixed Every external cache should be 1 day
     *
     * Every external cache should be 1 day
     */
    protected function get_cache($url)
    {
        $this->CI->load->model('cache_external_api_model');
        $res = $this->CI->cache_external_api_model->find_by([
            'url' => md5($url),
            'expired_at >=' => business_date('Y-m-d H:i:s')
        ], [
            'master' => TRUE
        ]);

        if ($res) {
            return json_decode($res->data, TRUE);
        }

        return FALSE;
    }

    /**
     * @param $url
     * @param $data
     */
    protected function set_cache($url, $data) {
        $this->CI->load->model('cache_external_api_model');
        $this->CI->cache_external_api_model->create([
            'url' => md5($url),
            'data' => json_encode($data),
            'expired_at' => date('Y-m-d H:i:s', strtotime('+30 days'))
        ], [
            'mode' => 'replace'
        ]);
    }

    /**
     * @param string $path
     * @param array $data
     * @param string $method
     * @param bool $is_json
     * @return bool|mixed
     */
    protected function _request($path = null, $data = array(), $method = 'get', $is_json = false)
    {
        $url = $this->api_base.trim($path, '/');
        $cache_url = $url . '?'. http_build_query($data);

        // Get cache
        if ($cache = $this->get_cache($cache_url)) {
            return json_decode($cache, true);
        }

        if (strtolower($method == 'get')) {
            $url = $url . '?'. http_build_query($data);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        if ($is_json) {
            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: '.strlen($data)
            ));
        } else if (strtolower($method) == 'post') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);
        if (!empty($info['http_code']) && 200 == $info['http_code']) {
            $this->set_cache($cache_url, $response);
            return json_decode($response, true);
        }

        return FALSE;
    }
}

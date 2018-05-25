<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . 'libraries/Google/Google_base.php';

/**
 * Google YouTube accessing Library
 */
class Google_Youtube extends Google_base
{
    /**
     * @var Google_Service_YouTube
     */
    protected $service = null;

    /**
     * Google_Youtube constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);

        $this->authorize();
        $this->service = $this->getInstance('Google_Service_YouTube');
    }

    /**
     * Finding channel from specific keyword
     *
     * @param array $params
     * @return mixed
     */
    public function search_channel($params = [])
    {
        $res = $this->service->search->listSearch('id,snippet', array_merge([
            'maxResults' => 20,
            'regionCode' => 'JP',
            'order' => 'relevance',
            'safeSearch' => 'strict',
            'type' => 'channel'
        ], $params));

        return $res->getItems();
    }

    /**
     * @param int $id
     * @return string
     */
    public function get_channel_url($id)
    {
        return 'https://www.youtube.com/channel/' . $id;
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function search_video($params = [])
    {
        $res = $this->service->search->listSearch('id,snippet', array_merge([
            'order' => 'viewCount',
            'type' => 'video',
            'maxResults' => 20
        ], $params));

        return $res->getItems();
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . 'libraries/Google/Google_base.php';

/**
 * Google Search accessing Library
 */
class Google_Search extends Google_base
{
    /**
     * @var Google_Service_Customsearch
     */
    protected $service = null;

    /**
     * Google_Search constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);

        $this->authorize();
        $this->service = $this->getInstance('Google_Service_Customsearch');
    }

    /**
     * Search
     *
     * @param string $text
     * @param array $params
     *
     * @return mixed
     */
    public function image_search($text, $params = [])
    {
        $res = $this->service->cse->listCse($text, array_merge([
            'imgType' => 'photo',
            'imgSize' => 'large',
            'cx' => $this->config['custom_engine_id']
        ], $params, [
            'searchType' => 'image'
        ]));

        return $res->getItems();
    }


}

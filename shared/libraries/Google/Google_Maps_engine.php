<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . 'libraries/Google/Google_base.php';

/**
 * Google Maps web service accessing Library
 */
class Google_Maps_engine extends Google_base
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
}

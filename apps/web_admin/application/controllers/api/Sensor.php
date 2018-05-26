<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/Api_controller.php';

/**
 * Class Sensor
 */
class Sensor extends Api_controller
{
    public function __construct()
    {
        parent::__construct();

        $this->_before_filter('_require_login', [
            'except' => ['get_list']
        ]);
    }

    /**
     * @return array
     * @throws APP_Api_internal_call_exception
     */
    public function get_list()
    {
        $sensors = $this->_internal_api('sensor', 'get_list', []);

        return $this->_true_json($sensors);
    }
}

<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Application_controller.php';

/**
 * Top controller
 */
class Top extends Application_controller
{
    /**
     * Top constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_sidebar = 'top';

        $this->_before_filter('_require_login', [
            'except' => ['index']
        ]);
    }

    /**
     * Index
     */
    public function index()
    {

    }
}

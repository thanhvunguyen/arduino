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
            'except' => ['index', 'get_ticket_status_in_device']
        ]);
    }

    /**
     * Index
     */
    public function index()
    {
        $view_data['events'] = $this->_internal_api('event', 'get_list_by_time_area', []);

        // Set title
        $this->_page_title = 'ダッシュボード';
        $this->_render($view_data);
    }
}

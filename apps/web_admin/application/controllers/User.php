<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Application_controller.php';

/**
 * User controller
 */
class User extends Application_controller
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_before_filter('_require_login', [
            'except' => ['login', 'logout']
        ]);
    }

    /**
     * Function login
     *
     * @throws APP_Api_internal_call_exception
     * @throws APP_DB_exception_duplicate_key_entry
     * @throws APP_Exception
     */
    public function login()
    {
        $view_data = [];

        if ($this->input->is_post()) {
            $params = $this->input->post();

            if ($params['login_id'] == 'admin' && $params['password'] == '12345678') {
                return redirect('/dashboard');
            }

            return $this->_render_404();
        }

        $this->_page_title = 'Login';

        $this->_render($view_data);
    }

}

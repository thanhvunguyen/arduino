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

        $this->_sidebar = 'top';
    }

    /**
     * TP0 - Login function
     */
    public function login()
    {
        // Check user logged in or not
        if (! empty($this->current_user->account_id)) {
            redirect('/');
            return;
        }

        $view_data = [];

        if ($this->input->is_post()) {
            $params = $this->input->post();

            // Call API login
            $res = $this->_api('auth')->login($params);

            // Check success
            if (! empty($res['success']) && !empty($res['submit'])) {
                redirect('/');
                return;
            }

            // If login fail
            $view_data['errmsg'] = isset($res['errmsg']) ? $res['errmsg'] : NULL;
            $view_data['post'] = $params;
        }

        $this->_page_title = 'トップ（ログイン）';

        $this->_render($view_data);
    }

    /**
     * Logout function
     */
    public function logout()
    {
        $this->_api('auth')->logout();

        $this->_redirect('/user/login');
    }

    /**
     * TP02 - Update name
     *
     * @author dungpt@nal.vn
     */
    public function update_name()
    {
        // Set breadcrumb
        $this->_breadcrumb = [
            [
                'link' => '/',
                'name' => 'TOP'
            ],
            [
                'name' => 'ユーザー名変更'
            ],
        ];

        // Set title
        $this->_page_title = 'ユーザー名変更';

        // Get info current account
        $account = [
            'name' => $this->current_user->name,
            'name_kana' => $this->current_user->name_kana,
        ];

        // Pass to view data
        $view_data['post'] = $account;

        if ($this->input->is_post()) {
            $post_data = $this->input->post();
            $post_data['is_update_name'] = TRUE;
            $post_data['account_id'] = $this->current_user->account_id;

            // Call API update end user password
            $res = $this->_api('account')->update_info($post_data);

            // Check success
            if (!empty($res['success']) && !empty($res['submit'])) {
                // View message update success
                $this->session->set_flashdata('update_info_success', t('notify_message.update_info_success'));
                $this->session->keep_flashdata('update_info_success');
                $view_data['message'] = $this->session->flashdata('update_info_success');
                $this->_find_current_user();
            }

            $view_data['form_errors'] = isset($res['invalid_fields']) ? $res['invalid_fields'] : [];
            $view_data['errmsg'] = isset($res['errmsg']) ? $res['errmsg'] : null;
            $view_data['post'] = $post_data;
        }

        return $this->_render($view_data);
    }

    /**
     * TP03 - Update email
     *
     * @author dungpt@nal.vn
     */
    public function update_email()
    {
        // Set breadcrumb
        $this->_breadcrumb = [
            [
                'link' => '/',
                'name' => 'TOP'
            ],
            [
                'name' => 'メールアドレス変更'
            ],
        ];

        // Set title
        $this->_page_title = 'メールアドレス変更';

        // Pass to view data
        $view_data['post'] = ['email' => $this->current_user->login_id];

        if (is_null($this->input->post('not_send_email')) && $this->input->is_post()) {
            $this->_api('account')->send_email_to_user([
                'account_id' => $this->current_user->account_id
            ]);
            $this->session->set_flashdata('send_email_success', t('notify_message.send_email_success'));
            $this->session->keep_flashdata('send_email_success');
            $view_data['message'] = $this->session->flashdata('send_email_success');
            $view_data['post'] = ['email' => $this->current_user->login_id];

            return $this->_render($view_data);
        }

        if ($this->input->is_post()) {
            $post_data = $this->input->post();
            $post_data['old_email'] = $this->current_user->login_id;
            $post_data['is_update_email'] = TRUE;
            $post_data['account_id'] = $this->current_user->account_id;

            // Call API update end user password
            $res = $this->_api('account')->update_info($post_data);

            // Check success
            if (! empty($res['success']) && !empty($res['submit'])) {
                $this->session->set_flashdata('update_info_success', t('notify_message.update_info_success'));
                $this->session->keep_flashdata('update_info_success');
                $view_data['message'] = $this->session->flashdata('update_info_success');
            }

            $view_data['form_errors'] = isset($res['invalid_fields']) ? $res['invalid_fields'] : [];
            $view_data['errmsg'] = isset($res['errmsg']) ? $res['errmsg'] : null;
            $view_data['post'] = $post_data;
        }

        return $this->_render($view_data);
    }

    /**
     * TP03 - Update password
     *
     * @author dungpt@nal.vn
     */
    public function update_password()
    {
        $view_data = [];

        // Set breadcrumb
        $this->_breadcrumb = [
            [
                'link' => '/',
                'name' => 'TOP'
            ],
            [
                'name' => 'パスワード変更'
            ],
        ];

        // Set title
        $this->_page_title = 'パスワード変更';

        if ($this->input->is_post()) {
            $post_data = $this->input->post();
            $post_data['is_update_password'] = TRUE;
            $post_data['account_id'] = $this->current_user->account_id;

            // Call API update end user password
            $res = $this->_api('account')->update_info($post_data);

            // Check success
            if (! empty($res['success']) && !empty($res['submit'])) {
                $this->session->set_flashdata('update_info_success', t('notify_message.update_info_success'));
                $this->session->keep_flashdata('update_info_success');
                $view_data['message'] = $this->session->flashdata('update_info_success');
            }

            $view_data['form_errors'] = isset($res['invalid_fields']) ? $res['invalid_fields'] : [];
            $view_data['errmsg'] = isset($res['errmsg']) ? $res['errmsg'] : null;
            $view_data['post'] = $post_data;
        }

        return $this->_render($view_data);
    }
}

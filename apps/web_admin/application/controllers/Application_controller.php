<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . 'controllers/modules/APP_Api_authenticatable.php';

/**
 * Application_controller
 *
 * @property User_model user_model
 * @property User_login_token_model user_login_token_model
 * @property APP_User_agent user_agent
 *
 * @package Controller
 * @version $id$
 * @copyright 2015- Interest Marketing, inc. (CONTACT info@interest-marketing.net)
 */
class Application_controller extends APP_Controller
{
    use APP_Api_authenticatable;

    public $layout = "layouts/base";

    /**
     * @var array Admin role
     */
    protected $role = [];

    /**
     * Application_controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // profilerを無効化
        $this->output->enable_profiler(FALSE);
    }

    /**
     * @param array $data
     * @param string $template_path
     * @param bool|TRUE $layout
     *
     * @throws APP_Api_internal_call_exception
     * @throws APP_DB_exception_duplicate_key_entry
     * @throws APP_Exception
     * @throws Exception
     */
    public function _render($data = [], $template_path = NULL, $layout = TRUE)
    {
        $data['meta'] = $this->_meta(
            !empty($data['meta']) ? $data['meta'] : []
        );

        if ($msg = $this->session->flashdata('msg')) {
            $data['flash_msg'] = $msg;
            $this->session->set_flashdata('msg', null);
        }

        if ($msg = $this->session->flashdata('msg_success')) {
            $data['flash_msg_success'] = $msg;
            $this->session->set_flashdata('msg_success', null);
        }

        parent::_render($data, $template_path, $layout);
    }

    /**
     * _meta
     *
     * Fetch meta information of HTML
     *
     * @access public
     * @param array $config
     * @return array
     */
    protected function _meta($config = [])
    {
        return [
            'title' =>  (isset($config['title']) ?
                    $config['title'] : $this->config->item('title')) .
                ' | ' . $this->config->item('service_name'),
            'description' => isset($config['description']) ?
                $config['description'] : $this->config->item('service_description'),
            'keywords' => isset($config['keywords']) ?
                implode(',', $config['keywords']) : $this->config->item('service_keywords'),

            'app_id' => $this->config->item('app_fbid'),
            'image' => isset($config['image']) ?
                $config['image'] : site_url('img/opg.png'),

            'canonical' => isset($config['canonical']) ?
                site_url() . preg_replace('/^\//', '', $config['canonical']) : null,
            'nexturl' => isset($config['nexturl']) ?
                site_url() . preg_replace('/^\//', '', $config['nexturl']) : null,
            'prevurl' => isset($config['prevurl']) ?
                site_url() . preg_replace('/^\//', '', $config['prevurl']) : null,
            'amphtml' => isset($config['amphtml']) ?
                site_url() . preg_replace('/^\//', '', $config['amphtml']) : null,

            'url' => current_url(),
            'type' => site_url() == current_url() ? 'website' : 'article',

            'copyright' => $this->config->item('service_copyright'),

            'breadcrumb' => isset($config['breadcrumb']) ?
                $config['breadcrumb'] : [],

            'site_name' => $this->config->item('service_name'),
            'site_description' => $this->config->item('service_description'),

            'without_header' => !empty($config['without_header']) ? TRUE : FALSE,
            'without_footer' => !empty($config['without_footer']) ? TRUE : FALSE,
        ];
    }

    /**
     * Find current user on user site
     */
    public function _find_current_user()
    {
        $this->load->model('user_model');

        $this->current_user = new APP_Anonymous_operator;
        APP_Model::set_operator($this->current_user);

        // If session has user_data
        $user_id = $this->session->userdata('user_id');

        // Because user not login
        if (empty($user_id)) {

            // Try to find cookie data to login
            $cname = md5('admin_id');
            $token = $this->input->cookie($cname);

            if (!$token) {
                return;
            }

            /** @var object $res */
            $this->load->model('user_login_token_model');
            $res = $this->user_login_token_model
                ->where('token', $token)
                ->first([
                    'master' => TRUE
                ]);

            if (!$res) {
                $this->input->cookie($cname, null);
                return;
            }

            $this->user_login_token_model->update($res->id, [
                'latest_login_at' => business_date('Y-m-d H:i:s'),
                'user_agent' => $this->input->user_agent()
            ]);

            // Set user session
            $user_id = $res->admin_id;
            $this->session->set_userdata('user_id', $user_id);

            if (!$user_id) {
                return;
            }
        }

        /** @var User_record $user */
        $user = $this->user_model->find($user_id);

        if (empty($user)) {
            return;
        }

        $this->current_user = $user;
        APP_Model::set_operator($this->current_user);
    }

    /**
     * Require login
     */
    public function _require_login()
    {
        if ($this->router->fetch_method() == 'login') {
            return;
        }

        if (!$this->current_user->is_login()) {
            $referrer = $this->agent->referrer();
            $this->_redirect('/login' . (!empty($referrer) ? '?r=' . urlencode($referrer) : ''));
        }
    }

    /**
     * Verify if has role
     */
    public function _has_role()
    {
        if (empty($this->role)) {
            return TRUE;
        }

        foreach ($this->role AS $v) {
            if ($this->current_user->has_role($v)) {
                return TRUE;
            }
        }

        $this->_render_404();
        return FALSE;
    }

    /**
     * Handle Exception
     *
     * @access public
     *
     * @param Exception $e
     *
     * @return bool 例外通知するかどうか
     * @throws APP_Api_call_exception
     * @throws Exception
     */
    public function _catch_exception(Exception $e)
    {
        // 内部APIエラー呼び出しの場合のエラーハンドリング
        if ($e instanceof APP_Api_call_exception) {

            switch ($e->getCode()) {

                // レコードが見つからない場合は 404 とする
                // 権限がない場合は、404表示とする
                // パラメータ不備の場合は、404表示とする
                case APP_Api::NOT_FOUND:
                case APP_Api::FORBIDDEN:
                case APP_Api::INVALID_PARAMS:
                    if (ENVIRONMENT != 'development') {
                        return $this->_render_404();
                    }
                    break;

                // 未認証の場合は ログイン認証処理 を呼び出すこととする
                case APP_Api::UNAUTHORIZED:
                    if (method_exists($this, '_require_login')) {
                        return $this->_require_login();
                    }
                    break;

                default:
                    break;
            }

        }

        throw $e;
    }

}

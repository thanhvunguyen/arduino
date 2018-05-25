<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once SHAREDPATH . 'controllers/modules/APP_Api_authenticatable.php';

/**
 * Application_controller
 *
 * @property APP_User_agent user_agent
 *
 * @package Controller
 * @version $id$
 * @copyright 2015- Interest Marketing, inc. (CONTACT info@interest-marketing.net)
 * @property Account_login_token_model account_login_token_model
 * @property Account_model account_model
 * @property Koen_model koen_model
 * @property Client_kogyo_model client_kogyo_model
 * @property App_id_model app_id_model
 */
class Application_controller extends APP_Controller
{
    use APP_Api_authenticatable;

    const DEFAULT_LIMIT = 20;

    /**
     * Layout default
     *
     * @var string
     */
    public $layout = "layouts/base";

    /**
     * Breadcrumb data
     *
     * @var null
     */
    protected $_breadcrumb = NULL;

    /**
     * Page title
     *
     * @var string
     */
    protected $_page_title = NULL;

    /**
     * Sidebar
     *
     * @var string
     */
    protected $_sidebar = NULL;

    /**
     * Admin role
     *
     * @var array Admin role
     */
    protected $role = [];

    /**
     * Application_controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_before_filter('_find_current_user');
        $this->_before_filter('_require_login');

        // profilerを無効化
        $this->output->enable_profiler(FALSE);

    }

    /**
     * Create pagination data
     *
     * @param array $params
     * @param int $total
     *
     * @return array
     */
    public function _paginate($params = [], $total = 0)
    {
        $param_output = $this->input->param();

        unset($param_output['ci_session']);

        return [
            'p' => isset($params['p']) ? (int) $params['p'] : 1,
            'total' => $total,
            'limit' => $params['limit'],
            'params' => $param_output,
            'const' => [
                'NUMBER_PAGE_SHOW' => 11,
                'GO_TO_10' => 10,
                'LIMITS' => [
                    'LIMIT_20' => 20,
                    'LIMIT_50' => 50,
                    'LIMIT_100' => 100,
                ]
            ]
        ];
    }

    /**
     * Check promoter exist in Event
     *
     * @param string $kogyo_code
     * @param string $kogyo_sub_code
     *
     * @return bool
     *
     * @author <hoangnq@nal.vn>
     */
    public function _required_has_permission_for_event($kogyo_code, $kogyo_sub_code)
    {
        if (! $this->current_user->is_administrator()) {
            $this->load->model('client_kogyo_model');

            return $this->client_kogyo_model->promoter_exist_in_event([
                'kogyo_code' => $kogyo_code,
                'kogyo_sub_code' => $kogyo_sub_code,
                'client_code' => $this->current_user->client_code
            ]);
        }

        return TRUE;
    }


    /**
     * Check promoter exist in Event
     *
     * @param string $kogyo_code
     * @param string $kogyo_sub_code
     *
     * @return bool
     *
     * @author <hoangnq@nal.vn>
     */
    public function _required_has_permission_for_app_id($app_id)
    {
        if (!$this->current_user->is_administrator()) {
            $this->load->model('client_kogyo_model');

            return $this->client_kogyo_model->promoter_exist_in_app_id([
                'app_id' => $app_id,
                'client_code' => $this->current_user->client_code
            ]);
        }

        return TRUE;
    }

    /**
     * Check exist Event
     *
     * @param string $kogyo_code
     * @param string $kogyo_sub_code
     * @param string $koen_code
     *
     * @return bool
     *
     * @author <hoangnq@nal.vn>
     */
    public function _required_exist_event($kogyo_code, $kogyo_sub_code, $koen_code)
    {
        $this->load->model('koen_model');

        return ! empty($this->koen_model->find_by([
            'kogyo_code' => $kogyo_code,
            'kogyo_sub_code' => $kogyo_sub_code,
            'koen_code' => $koen_code
        ]));
    }

    /**
     * Check exist App id
     *
     * @param string $app_id
     *
     * @return bool
     *
     * @author <hoangnq@nal.vn>
     */
    public function _required_exist_app_id($app_id)
    {
        $this->load->model('app_id_model');

        return !empty($this->app_id_model->find_by([
            'app_id' => $app_id,
        ]));
    }


    /**
     * Add params when use role promoter
     *
     * @param array $params
     */
    public function _for_promoter($params = [])
    {
        $params['promoter_id'] = $this->current_user->promoter_id;
    }

    /**
     * Add params when use role promoter
     */
    public function _require_operater()
    {
        if (! $this->current_user->is_administrator()) {
            $this->_redirect('/login');
        }
    }

    /**
     * Set params of list
     *
     * @param array $params
     * @internal param int $limit
     * @internal param int $offset
     *
     * @return array
     */
    public function _params($params = [])
    {
        $params = array_merge($this->input->param(), $params);

        if (empty($params)) {
            $params = [];
        }

        if (empty($params['limit'])) {
            $params['limit'] = self::DEFAULT_LIMIT;
        }

        if (empty($params['offset'])) {
            $params['offset'] = 0;
        }

        if (!empty($params['p']) && is_numeric($params['p']) && $params['p'] > 0) {
            $params['offset'] = ($params['p'] - 1) * $params['limit'];
        } else {
            unset($params['p']);
        }

        return $params;
    }

    /**
     * Render page
     *
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
            $this->session->set_flashdata('msg', NULL);
        }

        if ($msg = $this->session->flashdata('msg_success')) {
            $data['flash_msg_success'] = $msg;
            $this->session->set_flashdata('msg_success', NULL);
        }

        // Assign page title
        if (isset($this->_page_title)) {
            $data['page_title'] = $this->_page_title;
        }

        // Assign breadcrumb
        if (isset($this->_breadcrumb)) {
            $data['breadcrumb'] = $this->_breadcrumb;
        }

        // Assign breadcrumb
        if (isset($this->_sidebar)) {
            $data['sidebar'] = $this->_sidebar;
        }

        $data['is_login'] = isset($data['is_login']) ? $data['is_login'] : TRUE;
        if ($this->router->fetch_method() == 'login') {
            unset($data['is_login']);
        }

        // Assign name of user
        $data['name_of_user'] = $this->current_user->_operator_name();

        // Assign is e+ operator
        $data['is_admin'] = $this->current_user->is_administrator();

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
            'title' => isset($this->_page_title) ? $this->_page_title : (isset($config['title']) ?
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
                site_url() . preg_replace('/^\//', '', $config['canonical']) : NULL,
            'nexturl' => isset($config['nexturl']) ?
                site_url() . preg_replace('/^\//', '', $config['nexturl']) : NULL,
            'prevurl' => isset($config['prevurl']) ?
                site_url() . preg_replace('/^\//', '', $config['prevurl']) : NULL,
            'amphtml' => isset($config['amphtml']) ?
                site_url() . preg_replace('/^\//', '', $config['amphtml']) : NULL,

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
        // Load model
        $this->load->model('account_model');

        $this->current_user = new APP_Anonymous_operator;
        APP_Model::set_operator($this->current_user);

        // Get user login
        $user_id_cookie = $this->get_user_remember_login();
        $user_id_session = $this->session->userdata('user_id');
        $user_id = ($user_id_cookie) ? $user_id_cookie : $user_id_session;

        if (empty($user_id)) {
            return;
        }

        $this->session->set_userdata('user_id', $user_id);

        // Load model
        $this->load->model('account_login_token_model');

        $res = $this->account_login_token_model->find_by([
            'account_id' => $user_id,
        ]);

        // If not sign in before
        if (empty($res)) {
            return;
        }

        // If is not auto login
        if (! $this->session->auto_login()) {
            if (time() >  strtotime($res->updated_at) + $this->session->_default_auto_logout) {
                $this->_api('auth')->logout();
                return;
            }
        }

        $this->account_login_token_model->update($user_id, [
            'user_agent' => $this->input->user_agent(),
            'remote_ip' => $this->input->ip_address()
        ]);

        $user = $this->account_model
            ->select('account_id, name, name_kana, login_password, account_type, client_code, login_id')
            ->select('invalid_flag, account_lock_flag, created_at, updated_at')
            ->find($user_id);

        if (empty($user) || $user->invalid_flag === FLAG_TRUE || $user->account_lock_flag === FLAG_TRUE) {
            return;
        }

        // If session has user_data
        $this->session->set_userdata('user_id', $user->account_id);
        $this->current_user = $user;
        APP_Model::set_operator($this->current_user);
    }

    private function get_user_remember_login()
    {
        $key_cookie = $this->config->item('key_user_login_cookie');
        $value = isset($_COOKIE[$key_cookie]) ? $_COOKIE[$key_cookie] : null;

        if (empty($value)) {
            return FALSE;
        }

        require_once SHAREDPATH . 'third_party/firebase/php_jwt/JWT.php';
        $jwt = new Firebase\JWT\JWT();

        try {
            $decode = $jwt->decode($value, $this->config->item('key_user_login'), ['HS256']);

            return $decode;
        } catch (Exception $exception) {
            return FALSE;
        }
    }

    /**
     * Verify if has role
     *
     * @return bool
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
     *
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

    /**
     * Require login
     */
    public function _require_login()
    {
        if ($this->router->fetch_method() == 'login') {
            return;
        }

        if (!$this->current_user->is_login()) {
            $this->_redirect('/user/login');
        }
    }

    /**
     * Get status of event
     *
     * @param $kogyo_code
     * @param $kogyo_sub_code
     * @param $koen_code
     * @return int
     */
    public function get_status_event($kogyo_code, $kogyo_sub_code, $koen_code)
    {
        $this->load->model('app_id_model');
        $event = $this->app_id_model->find_by([
            'kogyo_code' => $kogyo_code,
            'kogyo_sub_code' => $kogyo_sub_code,
            'koen_code' => $koen_code
        ]);

        if ($event->is_closed == APP_ID_EVENT_IS_NOT_CLOSE && ($event->is_has_dynamo == HAS_NO_DYNAMO_DB || $event->is_has_dynamo == CREATING_DYNAMO_DB)) {
            return EVENT_OPEN;
        } else if ($event->is_closed == APP_ID_EVENT_IS_CLOSED && $event->is_has_dynamo == HAS_NO_DYNAMO_DB) {
            return EVENT_CLOSED;
        }

        return EVENT_IN_PROGRESSING;
    }

    /**
     * @param $app_id
     * @return int
     */
    public function get_status_app_id($app_id)
    {
        $this->load->model('app_id_model');
        $app_id_data = $this->app_id_model->find_by([
            'app_id' => $app_id,
        ]);

        if ($app_id_data->is_closed == APP_ID_EVENT_IS_NOT_CLOSE && $app_id_data->is_has_dynamo == HAS_NO_DYNAMO_DB) {
            return EVENT_OPEN;
        } else if ($app_id_data->is_closed == APP_ID_EVENT_IS_CLOSED && $app_id_data->is_has_dynamo == HAS_NO_DYNAMO_DB) {
            return EVENT_CLOSED;
        } else if ($app_id_data->is_has_dynamo == CREATING_DYNAMO_DB) {
            return CREATING_DYNAMO_DB;
        }

        return EVENT_IN_PROGRESSING;
    }

    /**
     * Function execute curl
     *
     * @param array $params
     * @return mixed
     */
    public function execute_curl($params = []) {

        $data_string = json_encode($params['data']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $params['url']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $params['method']);

        if ($params['method'] == 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-type: image/jpeg',
            'x-amz-server-side-encryption-customer-algorithm:' . $params['x-amz-server-side-encryption-customer-algorithm'],
            'x-amz-server-side-encryption-customer-key:' . $params['x-amz-server-side-encryption-customer-key']
        ]);

        return curl_exec($ch);
    }

}

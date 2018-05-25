<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . 'controllers/modules/APP_Api_authenticatable.php';

/**
 * Application_controller
 *
 * @property APP_Config config
 * @property object agent
 * @property APP_Output output
 * @property User_model user_model
 *
 * @package Controller
 * @version $id$
 */
class Application_controller extends APP_Controller
{
    use APP_Api_authenticatable;

    public $layout = "layouts/base";

    /**
     * @var array Current logger
     */
    public $current_logger = null;

    public $current_language = 'en';

    /**
     * Application_controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_before_filter('_find_current_user');
        $this->_before_filter('_find_current_logger');
        $this->_before_filter('_find_language');

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
    public function _render($data = [], $template_path = null, $layout = TRUE)
    {
        // Set current language
        $data['current_language'] = $this->current_language;

        $data['meta'] = $this->_meta(
            !empty($data['meta']) ? $data['meta'] : []
        );

        parent::_render($data, $template_path, $layout);
    }

    /**
     * 404 APIレスポンス
     * 404ページ
     *
     * @access public
     *
     * @param string $message
     * @param string $sub_message
     * @param string $format
     */
    public function _render_404($message = '', $sub_message = '', $format = NULL)
    {
        if (empty($message)) {
            $message = 'Page not found';
        }

        if (empty($sub_message)) {
            $sub_message = 'Access page has been removed or moved.';
        }

        $this->_render(
            [
                'meta' => [
                    'title' => 'Page not found'
                ],
                'current_language' => $this->current_language,

                'message' => $message,
                'sub_message' => $sub_message
            ],
            $format == 'amp' ? 'errors/error_404_amp.html' : 'errors/error_404.html',
            $format == 'amp' ? 'layouts/base_amp.html' : 'layouts/base.html'
        );
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
            'title' => isset($config['title']) ? $config['title'] : $this->config->item('title'),

            'short_title' => isset($config['short_title']) ? $config['short_title'] : $config['title'],

            'description' => isset($config['description']) ? $config['description'] : '',

            'keywords' => isset($config['keywords'])
                ? implode(',', $config['keywords'])
                : $this->config->item('service_keywords'),

            'image' => isset($config['image']) ? $config['image'] : null,

            'url' => current_url(),
            'type' => site_url() == current_url() ? 'website' : 'article',

            'copyright' => $this->config->item('service_copyright'),

            'breadcrumb' => isset($config['breadcrumb']) ? $config['breadcrumb'] : [],

            'site_name' => $this->config->item('service_name'),

            'without_header' => !empty($config['without_header']) ? TRUE : FALSE,
            'without_footer' => !empty($config['without_footer']) ? TRUE : FALSE,
        ];
    }

    /**
     * Require login
     */
    public function _require_login()
    {
        if (!$this->current_user->is_login()) {
            $this->_redirect('/');
        }
    }

    /**
     * @throws APP_Api_internal_call_exception
     */
    public function _find_current_logger()
    {
        $agent = $this->agent->agent_string();
        if (!preg_match('/cloudfront/i', $agent)) {
            $this->current_logger = $this->_internal_api('user_logger', 'create_user');
        }
    }

    /**
     * Find current user on user site
     */
    public function _find_current_user()
    {
        $this->load->model('user_model');
        $this->load->library('session');

        $this->current_user = new APP_Anonymous_operator;
        APP_Model::set_operator($this->current_user);

        // If session has user_data
        $user_id = $this->session->userdata('user_id');

        if (empty($user_id)) {

            // Keep continue authentication
            $this->load->helper('cookie');
            $cname = null;
            $token = null;
            if (is_array($this->input->cookie())) {
                foreach (array_keys($this->input->cookie()) AS $k) {
                    if (!preg_match('/^SMART_TICKET_USER\_/', $k)) {
                        continue;
                    }

                    $cname = $k;
                    $token = $this->input->cookie($k);
                }
            }

            if ($cname && $token) {
                $res = $this->user_model->get_autologin($token);

                if ($res && ('SMART_TICKET_USER_' . md5($res->token) === $cname)) {
                    $user_id = (int) $res->user_id;
                }
            }

            //ここまで来てUserIDが存在しない場合
            if (!$user_id) {
                return;
            }
        }

        /** @var APP_Operator $user */
        $user = $this->user_model->find($user_id);

        if (empty($user)) {
            return;
        }

        $this->current_user = $user;
        APP_Model::set_operator($this->current_user);
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

    /**
     * コールバックを実行してフォーマットに応じた結果を返す
     *
     * @property object output
     *
     * @access protected
     * @param string $format
     * @param callable $callback
     * @param array $options
     */
    protected function respond_to($format, $callback, $options = [])
    {
        try {
            $result = $callback();
        } catch (APP_Exception $e) {
            // 何もしない
        } catch (Exception $e) {
            log_exception("ERROR", $e);
        }

        switch ($format) {
            // JSON
            case 'json':
                $this->_true_json(empty($result) ? null : $result);
                break;

            // GIF
            case 'gif':
                $this->output->enable_profiler(FALSE);
                $this->output->set_content_type('image/gif');
                $this->output->set_output(file_get_contents(APPPATH . "/views/tracker/space.gif"));
                break;

            // HTML
            case 'html':
                // 何もしない
                $this->output->set_output("");
                break;

            // XML
            case 'xml':
                // 何もしない
                $this->output->set_content_type('application/xml');
                break;

            // gz
            case 'gz':
                // 何もしない
                $this->output->set_content_type('application/x-gzip');
                break;

            default:
                // 何もしない
                $this->output->set_content_type('text/plain');
                $this->output->set_output("");
                break;
        }
    }
}

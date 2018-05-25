<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !class_exists('CI_Email')) {
    require_once BASEPATH . 'libraries/Email.php';
}

/**
 * Class APP_Email extend CI_Email
 */
class APP_Email extends CI_Email {

    /**
     * テンプレートモデル
     * @var string
     */
    protected $template_model = "mail_template_model";

    /**
     * テンプレートキャッシュ
     * @var string
     */
    protected $template_cache = [];

    /**
     * コンストラクタ
     *
     * @access public
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $files = array(
                SHAREDPATH . "config/email.php",
                SHAREDPATH . "config/" . ENVIRONMENT . "/email.php",
                APPPATH . "config/email.php",
                APPPATH . "config/" . ENVIRONMENT . "/email.php"
            );

            foreach ($files as $f) {
                if (is_file($f)) {
                    include $f;
                }
            }

            if (!empty($email)) {
                $config = $email;
            }
        }

        if (count($config) > 0) {
            $this->initialize($config);
        }

        log_message('debug', "Email Class Initialized");
    }

    /**
     * ヘッダーを追加
     *
     * @access public
     * @param string $name
     * @param string $value
     */
    public function header($name, $value)
    {
        $this->_headers[$name] = $value;
    }

    /**
     * 送信先設定
     *
     * @access public
     * @param string
     * @param string
     * @param bool
     * @return object
     */
    public function _to($to, $name = NULL, $point = FALSE)
    {
        $this->to($to);
        return $this;
    }

    /**
     * テンプレートアサイン
     *
     * @access public
     * @param int $template_id
     * @param array $attributes
     *
     * @return APP_Email
     */
    public function template($template_id, $attributes = array())
    {
        if (FALSE === ($result = $this->parse_template($template_id, $attributes))) {
            return $this;
        }

        $this->subject($result['subject']);
        if ($result['content_type'] === "html") {
            $this->html($result['content']);
        } else {
            $this->message($result['content']);
        }

        if (!empty($result['from_address'])) {
            $this->from($result['from_address'], $result['from_name']);
        }

        return $this;
    }

    /**
     * テンプレートデータからパースする
     *
     * @access public
     * @param int $template_id
     * @param array $attributes
     *
     * @return APP_Email
     */
    protected function parse_template($template_id, $attributes = array())
    {
        $CI =& get_instance();
        if (empty($this->template_cache[(int)$template_id])) {
            $CI->load->model($this->template_model);

            $template = $CI->{$this->template_model}->find($template_id, array('master' => TRUE));
            if (empty($template)) {
                log_message("ERROR", "APP_Email::template() template(ID:{$template_id}) is not found.");
                return FALSE;
            }

            $this->template_cache[(int)$template_id] = $template;
        } else {
            $template = $this->template_cache[(int)$template_id];
        }

        $CI->load->library('parser');

        $subject = $CI->parser->parse_string($template->subject, $attributes, TRUE);
        $content = $CI->parser->parse_string($template->content, $attributes, TRUE);

        return array(
            'subject' => $subject,
            'content' => $content,
            'content_type' => $template->content_type,
            'from_name' => $template->from_name,
            'from_address' => $template->from_address
        );
    }

}

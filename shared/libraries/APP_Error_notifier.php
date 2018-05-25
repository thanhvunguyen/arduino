<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class APP_Error_notifier {

    const MASKED_LABEL = "[MASKED]";

    /**
     * エラー通知ドライバ
     * @var string
     */
    protected $driver = NULL;

    /**
     * エラー通知プロジェクト名
     * @ver string
     */
    public $project = NULL;

    /**
     * エラーメール通知用テンプレート
     * @ver string
     */
    public $template_path = NULL;

    /**
     * エラーメール通知用デフォルトテンプレート
     * @ver string
     */
    protected $default_template =<<<EOT
[Project]: {project}
Environment: {environment}

[Message]:
{message}

[Where]:
{where}

[Arguments]: {arguments}
{name}: {value} {/arguments}

[URL]:
{request_uri}

[GET]: {get}
{name}: {value} {/get}

[POST]: {post}
{name}: {value} {/post}

[SERVER]: {server}
{name}: {value} {/server}

[BACKTRACE]:
{backtrace}

EOT;

    /**
     * マスクパラメータ一覧
     * @var array
     */
    protected $masked_parameters = array(
        "token", "password", "password_confirmation", "email", "address", "phone"
    );

    protected $CI = NULL;

    public function __construct($options = [])
    {
        // TODO: APP_Logと構成が違うので、いつか直す
 
        $this->CI =& get_instance();

        $this->sendable = FALSE;
        $this->project = basename(FCPATH);

        $this->CI->config->load('error_notifier', TRUE);

        $keys = array('sendable', 'project', 'masked_parameters', 'templete_path');

        foreach ($keys as $key) {
            $value = $this->CI->config->item($key, 'error_notifier');
            if (FALSE !== $value) {
                $this->{$key} = $value;
            }
        }

        switch ($this->CI->config->item('driver', 'error_notifier')) {
        case 'amazon':
            require_once SHAREDPATH . 'libraries/Error_notifier/AmazonSNS.php';
            $this->driver = new APP_Error_notifier_driver_amazon_sns(array(
                'key' => $this->CI->config->item('key', 'error_notifier'),
                'secret' => $this->CI->config->item('secret', 'error_notifier'),
                'region' => $this->CI->config->item('region', 'error_notifier'),
                'topic' => $this->CI->config->item('topic', 'error_notifier'),
            ));
            break;

        default:
            require_once SHAREDPATH . 'libraries/Error_notifier/Email.php';
            $this->driver = new APP_Error_notifier_driver_email(array(
                'to' => $this->CI->config->item('to', 'error_notifier'),
                'from' => $this->CI->config->item('from', 'error_notifier')
            ));
            break;
        }
    }

    /**
     * 例外通知
     *
     * @access public
     * @param object $exception
     * @param array $options
     * @return bool
     */
    public function send_exception($exception, $options = [])
    {
        $subject = get_class($exception) . ": " . mb_substr($exception->getMessage(), 0, 255);

        $message = sprintf("%s at %s:%d",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        $lines = explode("\n", $message);
        $a = [];
        foreach ($lines as $line) {
            $len = mb_strlen($line);

            if ($len == 0) {
                $a[] = '';
            }

            $p = 0;
            while ($p < $len) {
                $t = mb_strimwidth($line, $p, 900);
                $a[] = $t;

                $p += mb_strlen($t);
            }
        }
        $message = implode("\n", $a);
        unset($lines, $line, $len, $a, $p, $t);

        return $this->send($message, array(
            'subject' => $subject,
            'exception' => $exception
        ));
    }

    /**
     * 通知
     *
     * @access public
     * @param string $message
     * @param array $options
     * @return bool
     */
    public function send($message, $options = [])
    {
        if ( ! $this->sendable) {
            return false;
        }

        $CI =& get_instance();

        $CI->load->library('parser');

        $options = array_merge(array(
            'template_path' => $this->template_path
        ), $options);

        if ( ! array_key_exists('subject', $options)) {
            $options['subject'] = $message;
        }

        $options['subject'] = sprintf("%s:[%s] %s", $this->project, $this->environment(), mb_substr($options['subject'], 0, 255));

        $lines = explode("\n", $message);
        $a = [];
        foreach ($lines as $line) {
            $len = mb_strlen($line);

            if ($len == 0) {
                $a[] = '';
            }

            $p = 0;
            while ($p < $len) {
                $t = mb_strimwidth($line, $p, 900);
                $a[] = $t;

                $p += mb_strlen($t);
            }
        }
        $message = implode("\n", $a);
        unset($lines, $line, $len, $a, $p, $t);

        $body_data = $this->body_arguments($message, $options);

        if (empty($options['template_path'])) {
            $mail_body = $CI->parser->parse_string($this->default_template, $body_data, TRUE);
        } else {
            $mail_body = $CI->parser->parse($options['template_path'], $body_data, TRUE);
        }

        $result = $this->driver->send($options['subject'], $mail_body);

        return $result;
    }

    /**
     * @param string $message
     * @param array $options
     * @return array
     */
    protected function body_arguments($message, $options = [])
    {
        $result = [
            'project' => $this->project,
            'environment' => $this->environment(),
            'message' => $message,
            'request_uri' => $this->request_uri(),
            'where' => $this->where(),
            'arguments' => $this->arguments(),
            'get' => $this->get(),
            'post' => $this->post(),
            'server' => $this->server()
        ];

        if (isset($options['exception'])) {
            $result['backtrace'] = $options['exception']->getTraceAsString();
        }

        return $result;
    }

    /**
     * @return mixed|string
     */
    protected function environment()
    {
        return ENVIRONMENT;
    }

    /**
     * @return mixed
     */
    protected function request_uri()
    {
        // TODO: proxyを考えていないURLの設計になっている
        return $this->mask_string(sprintf("%s://%s%s",
            empty($_SERVER['HTTPS']) ? "http" : "https",
            $_SERVER['SERVER_NAME'],
            $_SERVER['REQUEST_URI']
        ));
    }

    protected function where()
    {
        return $this->CI->router->fetch_class() . "::" . $this->CI->router->fetch_method(); 
    }

    protected function arguments()
    {
        return $this->array_to_body_arguments(array_slice($this->CI->uri->rsegment_array(), 2));
    }

    protected function get()
    {
        return $this->array_to_body_arguments($_GET);
    }

    protected function post()
    {
        return $this->array_to_body_arguments($_POST);
    }

    protected function server()
    {
        $server = [];
        foreach ($_SERVER as $key => $value) {
            if (preg_match("/^(HTTP_|REMOTE_|SERVER_|DOCUMENT_ROOT)/", $key)) {
                $server[$key] = $value;
            }
        }
        ksort($server);
        return $this->array_to_body_arguments($server);
    }

    protected function mask_string($string)
    {
        // TODO: もっとまともなマスク方法を実装する
        foreach ($this->masked_parameters as $parameter) {
            $string = preg_replace("/$parameter=.*?(&|$)/", sprintf("%s=%s$1", $parameter, self::MASKED_LABEL), $string);
        }
        return $string;
    }

    protected function array_to_body_arguments($array)
    {
        $parameters = [];
        foreach ($array as $key => $value) {
            $parameters[] = array('name' => $key, 'value' => in_array($key, $this->masked_parameters) ? self::MASKED_LABEL : $value);
        }
        return $parameters;
    }
}


<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . 'libraries/APP_Api.php';

/**
 * Base API
 *
 * @property APP_Loader load
 * @property CI_Session session
 * @property CI_Lang lang
 * @property APP_Config config
 * @property APP_Smarty smarty
 * @property APP_Email email
 * @property APP_Input input
 */
class Base_api extends APP_Api
{
    const USER_NOT_FOUND = 41010;
    const TOKEN_NOT_FOUND = 41020;
    const USER_IS_INACTIVE = 41030;

    /**
     * Standard Validator Class
     *
     * @var string
     */
    public $validator_name = 'Base_api_validation';

    /**
     * @var Base_api_validation
     */
    public $validator = NULL;

    /**
     * Array schema for csv
     */
    public $_schemas = NULL;

    /**
     * Identify email subject title
     *
     * @var array subject japan email
     */
    public $subject_jp_email = [
        
    ];

    /**
     * @param $path
     * @param array $config
     * @param array $data
     * @throws SmartyException
     */
    public function send_mail($path, $config = [], $data = [])
    {
        // Load the library
        $this->load->library('smarty');

        $res = $this->smarty->view(SHAREDPATH . 'views/' . $path, array_merge($data, [
            'site_name' => $this->config->item('site_name')
        ]), TRUE);

        // Remove un-use resource
        unset($this->smarty);

        // Send
        $this->load->library('email');
        $this->email
            ->from($config['from'], $this->config->item('mail_from_name'))
            ->to($config['to'], !empty($config['to_name']) ? $config['to_name'] : null)
            ->subject($config['subject'])
            ->message($res)
            ->set_mailtype(isset($config['mail_type']) ? 'html' : 'text')
            ->send();
    }

}

/**
 * Class Base_api_validation
 */
class Base_api_validation extends APP_Api_validation
{

}

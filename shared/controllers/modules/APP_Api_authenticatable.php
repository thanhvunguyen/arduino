<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API認証用モジュール
 *
 * API認証制御を入れ込んでくれるモジュール
 *
 * @property APP_Loader load
 * @property APP_Input input
 *
 * @method void _before_filter(String $method_name, Array $options = [])
 * @method void _false_json(Int $code, String $errmsg = null)
 *
 * @package APP\Controller
 * @copyright Interest Marketing, inc. (CONTACT info@interest-marketing.net)
 * @author Yoshikazu Ozawa <ozawa@interset-marketing.net>
 */
trait APP_Api_authenticatable
{

}



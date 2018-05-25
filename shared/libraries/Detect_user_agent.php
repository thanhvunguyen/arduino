<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . "libraries/APP_User_agent.php";

/**
 * User Agent クラス
 *
 * @author Yoshikazu Ozawa
 * @uses Net_UserAgent_Mobile
 */
class Detect_user_agent extends APP_User_agent {

    public function set_agent($user_agent)
    {
        if (isset($user_agent))
        {
            $this->agent = trim($user_agent);
        } else {
            $this->agent = null;
        }

        if ( ! is_null($this->agent))
        {
            if ($this->_load_agent_file())
            {
                $this->_compile_data();
            }
        }

        return $this->agent ? TRUE : FALSE;
    }

    /**
     * Androidかどうか
     *
     * @access public
     * @return bool
     */
    public function is_android()
    {
        return strpos($this->agent, 'Android') !== false;
    }

}
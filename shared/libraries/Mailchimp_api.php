<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . '/../vendor/drewm/mailchimp-api/src/MailChimp.php';

use \DrewM\MailChimp\MailChimp;

class Mailchimp_api extends MailChimp {

    /**
     * Mailchimp_api constructor.
     */
    public function __construct()
    {
        $files = array(
            SHAREDPATH . "config/mailchimp.php",
            SHAREDPATH . "config/" . ENVIRONMENT . "/mailchimp.php",
            APPPATH . "config/mailchimp.php",
            APPPATH . "config/" . ENVIRONMENT . "/mailchimp.php"
        );

        foreach ($files as $f) {
            if (is_file($f)) {
                include $f;
            }
        }

        /** @var array $mailchimp */
        parent::__construct($mailchimp['api_key']);
    }

}
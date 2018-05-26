<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once SHAREDPATH . '/core/APP_Model.php';

class Arduino_device_model extends APP_Model
{
    /**
     * Database name
     *
     * @var string
     */
    public $database_name = DB_MAIN;

    /**
     * Table name
     *
     * @var string
     */
    public $table_name = 'arduino_device';

    /**
     * Primary key
     *
     * @var string
     */
    public $primary_key = 'arduino_cd';
}
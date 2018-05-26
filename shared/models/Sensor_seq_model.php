<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once SHAREDPATH . '/core/APP_Model.php';

class Sensor_seq_model extends APP_Model
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
    public $table_name = 'sensor_seq';

    /**
     * Primary key
     *
     * @var string
     */
    public $primary_key = 'sensor_seq';
}
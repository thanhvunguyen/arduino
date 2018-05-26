<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once SHAREDPATH . '/core/APP_Model.php';

class Device_sensor_model extends APP_Model
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
    public $table_name = 'device_sensor';

    /**
     * Primary key
     *
     * @var string
     */
    public $primary_key = 'sensor_cd';

    /**
     *
     */
    public function get_list_sensor() {

        $this->select('device_sensor.sensor_cd, device_sensor.sensor_nm')
            ->select('infor_sensor.sensor_seq, infor_sensor.sensor_temp, infor_sensor.sensor_humi')
            ->select('sensor_seq.sensor_time');

        $this->join('infor_sensor', 'device_sensor.sensor_cd = infor_sensor.sensor_cd');

        $this->join('sensor_seq', 'infor_sensor.sensor_seq = sensor_seq.sensor_seq');

        $this->where('DATE(sensor_seq.sensor_time)', '2018-05-17');

        return $this->all();
    }
}
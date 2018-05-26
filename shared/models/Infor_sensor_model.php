<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once SHAREDPATH . '/core/APP_Model.php';

class Infor_sensor_model extends APP_Model
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
    public $table_name = 'infor_sensor';

    /**
     * Primary key
     *
     * @var string
     */
    public $primary_key = ['sensor_cd', 'sensor_seq'];

    /**
     * @param null $sensor_cd
     * @return array
     */
    public function get_sensor_detail($sensor_cd = null) {

        if (empty($sensor_cd)) {
            return [];
        }

        $this->select('infor_sensor.sensor_seq, infor_sensor.sensor_temp, 
            infor_sensor.sensor_humi')
            ->select('sensor_seq.sensor_time');

        $this->join('sensor_seq', 'infor_sensor.sensor_seq = sensor_seq.sensor_seq');

        $this->where('infor_sensor.sensor_cd', $sensor_cd);

        $this->order_by('sensor_seq.sensor_time', 'DESC');

        return $this->all();
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once SHAREDPATH . 'libraries/API/Base_api.php';

/**
 * Class Sensor_api
 *
 * @property Device_sensor_model device_sensor_model
 */
class Sensor_api extends Base_api
{
    /**
     * @return array
     * @throws Exception
     */
    public function get_list()
    {
        $this->load->model('device_sensor_model');

        $sensors = $this->device_sensor_model->get_list_sensor();

        $result = [];

        if (!empty($sensors)) {
            foreach ($sensors AS $sensor) {
                $result[$sensor->sensor_cd][] = [
                    'sensor_seq' => $sensor->sensor_seq,
                    'sensor_temp' => $sensor->sensor_temp,
                    'sensor_humi' => $sensor->sensor_humi,
                    'sensor_time' => $sensor->sensor_time
                ];
            }
        }

        return $this->true_json($result);
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Application_controller.php';

/**
 * APIコントローラ
 *
 * 全てのAPIコントローラはこのクラスを継承する
 *
 * @package Controller
 */
class Api_controller extends Application_controller
{
    public $is_api = TRUE;

    /**
     * Render error
     *
     * @param string $message
     * @param string $submessage
     * @param string $status
     * @param array $options
     */
    public function _render_error($message, $submessage, $status = '500', $options = [])
    {
        if (! isset($options['format']) || empty($options['format'])) {
            $options['format'] = 'json';
        }

        parent::_render_error($message, $submessage, $status, $options);
    }

    /**
     * Render content
     *
     * @param array $data
     * @param null $template_path
     *
     * @return array
     */
    public function _render_content($data = [], $template_path = NULL)
    {
        $this->_skip_action();
        if (! isset($template_path)) {
            return [];
        }

        $data['dformat'] = '%Y.%-m.%-d %-H:%M';

        $engine =& $this->_template_engine();

        return $engine->view($template_path, $data, TRUE);
    }
}

<?php

/**
 * @param array $params
 * @param APP_Smarty $smarty
 * @return string
 */
function smarty_function___($params, &$smarty)
{
    $name = $params['s'];

    if (isset($params['keep'])) {
        return $name;
    }

    $CI =& get_instance();
    $CI->load->library('multi_language_api');

    $lang = !empty($params['lang']) ? $params['lang'] :
        (!empty($CI->current_language) ? $CI->current_language : 'en');

    $str = $CI->multi_language_api->get_lang($name, $lang);

    if ($str !== null) {
        return $str;
    }

    $CI->multi_language_api->set_lang($name, 'en');
    return $name;
}

<?php

/**
 * @param array $params
 * @param APP_Smarty $smarty
 * @return string
 */
function smarty_function___t($params, &$smarty)
{
    $key = $params['key'];
    $tour = $params['tour'];

    $CI =& get_instance();

    $lang = !empty($params['lang']) ? $params['lang'] :
        (!empty($CI->current_language) ? $CI->current_language : 'en');

    if (!empty($tour['languages'][$lang])) {
        return $tour['languages'][$lang][$key];
    }

    return $tour[$key];
}

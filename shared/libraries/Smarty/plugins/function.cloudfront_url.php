<?php

/**
 * @param array $params
 * @param APP_Smarty $smarty
 * @return string
 */
function smarty_function_cloudfront_url($params, &$smarty)
{
    $CI =& get_instance();

    if (!empty($CI->config->item('cloudfront_url'))) {
        return $CI->config->item('cloudfront_url');
    }

    return '';
}

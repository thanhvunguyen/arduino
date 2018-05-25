<?php

function smarty_function_tour_url($params, &$smarty)
{
    $CI =& get_instance();
    $CI->load->helper('tour_helper');

    return tour_url($params['tour']['id'], !empty($params['lang']) ? $params['lang'] : null);
}

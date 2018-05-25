<?php

/**
 * @param array $params
 * @param APP_Smarty $smarty
 * @return string
 */
function smarty_function_cart_url($params, &$smarty)
{
    $lang = !empty($params['lang']) ? $params['lang'] : 'en';

    $p = [
        'tour_id' => $params['tour_id'],
        'event_id' => $params['event_id']
    ];

    if (!empty($params['event_date_id'])) {
        $p['event_date_id'] = $params['event_date_id'];
    }

    return sprintf('/cart/add_ticket/%s?', $lang).http_build_query($p);
}

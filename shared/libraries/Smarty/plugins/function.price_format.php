<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {pager} function plugin
 *
 * Type function
 * Name url
 *
 * @param array $params
 * @param APP_Smarty $smarty
 * @return null|string
 */
function smarty_function_price_format($params, &$smarty)
{
    if (is_null($params['price']['number'])) {
        return '-';
    }

    return $params['price']['format'];
}

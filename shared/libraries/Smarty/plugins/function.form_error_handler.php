<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * @param array $params
 * @param APP_Smarty $smarty
 * @return string
 */
function smarty_function_form_error_handler($params, &$smarty)
{
    $obj = $smarty->getTemplateVars('error');
    if (empty($obj)) {
        return "";
    }

    if (!empty($obj['invalid_fields'][$params['name']])) {
        return " has-error";
    }

    return "";
}

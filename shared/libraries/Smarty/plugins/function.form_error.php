<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty render image link
 * @param array $params
 * @param APP_Smarty $smarty
 * @return string
 */
function smarty_function_form_error($params, &$smarty)
{
    $obj = $smarty->getTemplateVars('error');
    if (empty($obj)) {
        return "";
    }

    if (!empty($obj['invalid_fields'][$params['name']])) {
        $cls = empty($params['class']) ? 'text-danger' : $params['class'];

        return sprintf('<div class="%s">%s</div>', $cls, $obj['invalid_fields'][$params['name']]);
    }

    return "";
}

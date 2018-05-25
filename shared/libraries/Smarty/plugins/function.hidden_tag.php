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
 * @internal param $key of image in db
 * @internal param $type of image in db (Ex: original|large|medium|small|tiny)
 *
 * @return string
 */
function smarty_function_hidden_tag($params, &$smarty)
{
    $q = !empty($params['params']) ? $params['params'] : [];

    $str = "";
    foreach ($q AS $k => $v) {


        if (isset($params['exclude'])) {
            if (in_array($k, $params['exclude'])) {
                continue;
            }
        }


        if (is_array($v)) {
            foreach ($v AS $sk => $sv) {
                $str .= sprintf('<input type="hidden" name="%s[]" value="%s">', htmlspecialchars($k, ENT_QUOTES), htmlspecialchars($sv, ENT_QUOTES));
            }
            continue;
        }

        $str .= sprintf('<input type="hidden" name="%s" value="%s">', htmlspecialchars($k, ENT_QUOTES), htmlspecialchars($v, ENT_QUOTES));
    }
    return $str;
}

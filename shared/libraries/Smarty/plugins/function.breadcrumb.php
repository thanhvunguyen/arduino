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
function smarty_function_breadcrumb($params, &$smarty)
{
    $inner = [];

    foreach ($params['vars'] AS $v) {

        if (is_array($v) && empty($v['name'])) {
            $tmp = [];
            foreach ($v AS $sv) {
                if (!empty($sv['url'])) {
                    $tmp[] = sprintf('<li><a href="%s">%s</a></li>', htmlspecialchars($sv['url'], ENT_QUOTES), htmlspecialchars($sv['name'], ENT_QUOTES));
                } else {
                    $tmp[] = sprintf('<li>%s</li>', htmlspecialchars($sv['name'], ENT_QUOTES));
                }
            }

            if (COUNT($tmp) > 0) {
                $inner[] =  count($tmp) >= 3 ?
                    implode(' ', array_splice($tmp, 0, 3)) . '<li>...</li>' : implode(' ', $tmp);
            }
            continue;
        }

        $split = !empty($params['split']) ? $params['split'] : '';

        if (!empty($v['url'])) {
            $tmp = sprintf('<li><a href="%s">%s</a> %s</li>', htmlspecialchars($v['url'], ENT_QUOTES), htmlspecialchars($v['name'], ENT_QUOTES), $split);
        } else {
            $tmp = sprintf('<li class="active">%s %s</li>', htmlspecialchars($v['name'], ENT_QUOTES), '');
        }

        $inner[] = $tmp;
    }

    return sprintf('<ol class="breadcrumb hidden-xs">%s</ol>',
        implode('', $inner)
    );
}

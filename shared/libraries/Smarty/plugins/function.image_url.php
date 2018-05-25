<?php

function smarty_function_image_url($params, &$smarty)
{
    $url = !empty($params['url']) ? $params['url'] : null;
    $type = !empty($params['type']) ? '/' . $params['type'] : '';

    if ($url) {
        return sprintf('%s%s', $url, $type);
    }

    return '/img/no_image.png';
}

<?php

function smarty_function_recommend_reason($params, &$smarty)
{
    $item = $params['item'];
    $str = '';

    switch (TRUE) {
        // https://en.wikipedia.org/wiki/Standard_score#/media/File:Normal_distribution_and_scales.gif

        // Upper 18.54%
        case $item['standard_score'] >= 60:
            $str = sprintf('Because you might like <strong>%s</strong> ...', $item['name']);
            break;

        case $item['standard_score'] >= 50:
            $str = sprintf('Also recommend <strong>%s</strong> ...', $item['name']);
            break;
    }

    return $str;
}
<?php

function smarty_function_pager($params, &$smarty)
{
    if (!array_key_exists('total', $params)) {
        throw new InvalidArgumentException('`total` is not set.');
    }

    if (!array_key_exists('limit', $params)) {
        throw new InvalidArgumentException('`limit` is not set.');
    }

    if (!array_key_exists('offset', $params)) {
        $params['offset'] = 0;
    }

    if (!array_key_exists('range', $params)) {
        $params['range'] = 5;
    }

    if (!array_key_exists('type', $params)) {
        $params['type'] = 'offset';
    }

    if (!array_key_exists('params', $params)) {
        $params['params'] = [];
    }

    $total = $params['total'];
    $offset = $params['offset'];
    $limit = $params['limit'];
    $range = $params['range'];
    $page_type = $params['type'];
    $params = $params['params'];

    if ($total == 0) {
        return "";
    }

    $total_page = ceil($total / $limit);
    $current_page = floor($offset / $limit) + 1;

    $CI =& get_instance();

    $path = parse_url($CI->input->server('REQUEST_URI'), PHP_URL_PATH);
    $query = parse_url($CI->input->server('REQUEST_URI'), PHP_URL_QUERY);

    parse_str($query, $q);
    $params = array_merge($q, $params);

    $html = '<ul class="pagination pagination-customize">';

    if ($current_page <= 1) {
        $html .= '<li><a class="hidden">&lt;</a></li>';
    } else {
        $previous = htmlspecialchars(sprintf("%s?%s", $path, http_build_query(array_merge($params, $page_type == 'page' ? ['p' => 1] : ['limit' => $limit, 'offset' => $offset - $limit]))), ENT_QUOTES);
        $html .= sprintf('<li><a href="%s">&lt;</a></li>', $previous);
    }

    $term = round(($range - 1) / 2);

    $start_page = $current_page - $term;
    $finish_page = $current_page + $term;

    if ($start_page < 1) {
        $finish_page += 1 - $start_page;
        $start_page = 1;
    }

    if ($finish_page > $total_page) {
        $start_page -= (1 + $finish_page - $total_page);
        $finish_page = $total_page;
    }

    if ($start_page < 1) {
        $start_page = 1;
    }

    if ($start_page > 1) {
        $p = htmlspecialchars(sprintf("%s?%s", $path, http_build_query(array_merge($params, $page_type == 'page' ? ['p' => 1] : ['limit' => $limit, 'offset' => 0]))), ENT_QUOTES);
        $html .= sprintf('<li><a href="%s">%s</a></li>', $p, 1);

        if ($start_page > 2) {
            $html .= '<li class="disabled"><a>...</a></li>';
        }
    }

    for ($i = $start_page; $i <= $finish_page; $i++) {
        $p = htmlspecialchars(sprintf("%s?%s", $path, http_build_query(array_merge($params,  $page_type == 'page' ? ['p' => $i] : ['limit' => $limit, 'offset' => $limit * ($i - 1)]))), ENT_QUOTES);

        if ($current_page == $i) {
            $html .= sprintf('<li class="active"><a>%s</a></li>', $i);
        } else {
            $html .= sprintf('<li class="%s"><a href="%s">%s</a></li>', $current_page == $i ? 'active' : '', $p, $i);
        }
    }

    if ($finish_page < $total_page) {
        if ($total_page - $finish_page > 1) {
            $html .= '<li class="disabled"><a>...</a></li>';
        }
        $p = htmlspecialchars(sprintf("%s?%s", $path, http_build_query(array_merge($params, $page_type == 'page' ? ['p' => $total_page] : ['limit' => $limit, 'offset' => $limit * ($total_page - 1)]))), ENT_QUOTES);
        $html .= sprintf('<li><a href="%s">%s</a></li>', $p, $total_page);
    }

    if ($current_page >= $total_page) {
        $html .= '<li ><a class="hidden">&gt;</a></li>';
    } else {
        $next = htmlspecialchars(sprintf("%s?%s", $path, http_build_query(array_merge($params, $page_type == 'page' ? ['p' => $total_page] : ['limit' => $limit, 'offset' => $offset + $limit]))), ENT_QUOTES);
        $html .= sprintf('<li><a href="%s">&gt;</a></li>', $next);
    }

    $html .= '</ul>';

    return $html;
}


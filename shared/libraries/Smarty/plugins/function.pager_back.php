<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {pager} function plugin
 *
 * Type function
 * Name url
 * @param array $params
 * @param APP_Smarty $smarty
 * @return null|string
 */
function smarty_function_pager($params, &$smarty)
{
    $offset = !empty($params['offset']) ? (int) $params['offset'] : 0;
    $limit = !empty($params['limit']) ? (int) $params['limit'] : 0;
    $total = !empty($params['total']) ? (int) $params['total'] : 0;
    $links = !empty($params['links']) ? (int) $params['links'] : 6;

    $pager_type = !empty($params['type']) ? $params['type'] : 'offset';

    if ($offset == 0 && $limit > $total) {
        return "";
    }

    if (!function_exists('site_url')) {
        $CI =& get_instance();
        $CI->load->helper('url_helper');
    }

    $info = parse_url($_SERVER['REQUEST_URI']);
    parse_str(!empty($info['query']) ? $info['query'] : '', $q);

    switch (TRUE) {
        case !empty($params['name']) :
            foreach ($q AS $k => $v) {
                if ($k == $params['name']) {
                    unset($q[$k]);
                    break;
                }
            }

            $q[$params['name']] = $params['page'];
            //$url = $info['path'] . '?' . http_build_query($q);
            break;

        default:
        case !empty($params['exclude']):
            foreach ($q AS $k => $v) {
                if (in_array($k, isset($params['exclude']) ? $params['exclude'] : [])) {
                    unset($q[$k]);
                    break;
                }
            }

            //$url = $info['path'] . '?' . http_build_query($q);
            break;
    }

    $prev = "";
    if ($offset > 0) {
        // Show first
        $url = (isset($params['base_url']) ? $params['base_url'] : '') .
            $info['path'] . '?' . http_build_query(array_merge($q, $pager_type == 'page' ? [
            ] : [
                'limit' => $limit,
                'offset' => 0
            ]));
        $prev = <<< EOM
<li>
    <a href="$url" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
    </a>
</li>
EOM;
    }

    $left = ($offset / $limit) - ceil($links / 2);
    $right = ($offset / $limit) + ceil($links / 2);
    $pages = ceil($total / $limit);
    $temp = [];

    var_dump($offset / $limit);
    var_dump($left, $right);

    if($left < 1){
        while($right <= $links){
            $right++;
        }
        $left = 1;
    }

    if($right > $pages){
        $left = $left - $right + $pages;
        $left = $left < 1 ? 1 : $left;
        $right = $pages;
    }

    for($i = $left + 1; $i <= $right; $i++){
        $temp[] = $i;
    }

    for($i = 0; $i < count($temp); $i++){
        $s = $temp[$i];

        $url = (isset($params['base_url']) ? $params['base_url'] : '') .
            $info['path'] . '?' . http_build_query(array_merge($q, $pager_type == 'page' ? [
                'p' => $s
            ] : [
                'limit' => $limit,
                'offset' => ($s - 1) * $limit
            ]));

        if ($offset == ($s - 1) * $limit) {
            $mid[] = sprintf('<li class="active"><a href="%s">%s</a></li>', $url, $s);
        } else {
            $mid[] = sprintf('<li><a href="%s">%s</a></li>', $url, $s);
        }
    }

    $next = "";
    if ($offset + $limit < $total) {
    // Show last
    $url = (isset($params['base_url']) ? $params['base_url'] : '') .
        $info['path'] . '?' . http_build_query(array_merge($q, $pager_type == 'page' ? [
            'p' => $pages
        ] : [
            'limit' => $limit,
            'offset' => ($pages - 1) * $limit
        ]));

    $next = <<< EOM
<li>
    <a href="$url" aria-label="Previous">
        <span aria-hidden="true">&raquo;</span>
    </a>
</li>
EOM;
    }

    $mid = implode('', $mid);
    $n = <<< EOM
<nav>
    <ul class="pagination">

        $prev

        $mid

        $next

    </ul>
</nav>
EOM;

    return $n;
}

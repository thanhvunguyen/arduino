<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = 'top';
$route['404_override'] = 'errors/error_404';

// Tour page
$route['(:any)/t/(:id)'] = "tour/detail/$2/$1";
$route['(:any)/t/(:id)/amp'] = "tour/detail/$2/$1/amp";
$route['(:any)/t/(:id)/(:any)'] = "tour/$3/$2/$1";
$route['(:any)/t/(:id)/(:any)/amp'] = "tour/$3/$2/$1/amp";
$route['(:any)/t/(:id)/(:any)/(:id)'] = "tour/$3/$2/$4/$1";
$route['(:any)/t/(:id)/(:any)/(:id)/amp'] = "tour/$3/$2/$4/$1/amp";

// Category & Category tag index $1 will be $lang
$route['(:any)/dive'] = "top/category/$1";
$route['(:any)/dive/(:any)/amp'] = "category/index/$2/$1/amp";
$route['(:any)/dive/(:any)/(:any)/amp'] = "category/tag/$2/$3/$1/amp";

$route['dive'] = "top/category/en";
$route['dive/(:any)/amp'] = "category/index/$1/en/amp";
$route['dive/(:any)/(:any)/amp'] = "category/tag/$1/$2/en/amp";

// Area index
$route['tokyo'] = "area/index/tokyo/en/amp";
$route['(:any)/tokyo'] = "area/index/tokyo/$1/amp";

$route['osaka-kyoto'] = "area/index/osaka-kyoto/amp";
$route['(:any)/osaka-kyoto'] = "area/index/osaka-kyoto/$1/amp";

$route['fukuoka-okinawa'] = "area/index/fukuoka-okinawa/amp";
$route['(:any)/fukuoka-okinawa'] = "area/index/fukuoka-okinawa/$1/amp";

$route['hokkaido'] = "area/index/hokkaido/amp";
$route['(:any)/hokkaido'] = "area/index/hokkaido/$1/amp";

$route['nagoya'] = "area/index/nagoya/amp";
$route['(:any)/nagoya'] = "area/index/nagoya/$1/amp";

$route['sendai'] = "area/index/sendai/amp";
$route['(:any)/sendai'] = "area/index/sendai/$1/amp";

$route['kitashinetsu'] = "area/index/kitashinetsu/amp";
$route['(:any)/kitashinetsu'] = "area/index/kitashinetsu/$1/amp";

$route['chugoku'] = "area/index/chugoku/amp";
$route['(:any)/chugoku'] = "area/index/chugoku/$1/amp";

$route['shikoku'] = "area/index/shikoku/amp";
$route['(:any)/shikoku'] = "area/index/shikoku/$1/amp";

// Images
$route['image/(:key)'] = "image/show/$1/$2";
$route['image/(:key)/(:type)'] = "image/show/$1/$2";


$route['login'] = "mypage/login";
$route['logout'] = "mypage/logout";
$route['signup'] = "mypage/signup";
$route['interest'] = "recommend/interest";

$route['trip'] = "recommend/result";
$route['trip/(:any)'] = "recommend/result/$1";

$route['api/tracker.gif'] = "api/tracker/index";

// Static pages
$route['about'] = "static_page/about/amp";
$route['privacy'] = "static_page/privacy/amp";

// Sitemap
$route['sitemap/index.xml'] = "sitemap/index";
$route['sitemap/(:bucket_key)'] = "sitemap/download/$1";

// Change language
$route['change_language'] = "top/change_language";
$route['change_language/(:key)'] = "top/change_language/$1";

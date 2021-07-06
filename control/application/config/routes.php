<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['index'] = 'dashboard/index';
$route['logout'] = 'login/logout';

$route['saldo_rewards/saldo/export/(.+)'] = 'saldo_rewards/export_saldo/index/$1';
$route['saldo_rewards/saldo/(.+)'] = 'saldo_rewards/saldo/index/$1';
$route['saldo_rewards/reward/export/(.+)'] = 'saldo_rewards/export_rewards/index/$1';
$route['saldo_rewards/reward/(.+)'] = 'saldo_rewards/reward/index/$1';
$route['saldo_rewards/topup/export/(.+)'] = 'saldo_rewards/export_topup/index/$1';
$route['saldo_rewards/topup/(.+)'] = 'saldo_rewards/topup/index/$1';


$route['transactions/detail/(.+)'] = 'transactions/detail/index/$1';
$route['transactions/export/(.+)'] = 'transactions/export/index/$1';
$route['transactions/printing/(.+)'] = 'transactions/printing/index/$1';

$route['transactions/cancel/submit'] = 'transactions/cancel/submit';
$route['transactions/cancel/(.+)'] = 'transactions/cancel/index/$1';

$route['(:any)/pages/(.+)'] = '$1/grid/$2';
$route['orders/items/pages/(.+)'] = 'orders/items';
$route['default_controller'] = "login";
$route['empty'] = "empty_cart";
$route['404_override'] = 'custom404';
$route['translate_uri_dashes'] = FALSE;

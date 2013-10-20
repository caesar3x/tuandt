<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
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
| There are two reserved routes:
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

$route['default_controller']                = 'sr_science/index';
$route['404_override']                      = 'pages';

$route['admin/help/([a-zA-Z0-9_-]+)']       = 'admin/help/$1';
$route['admin/([a-zA-Z0-9_-]+)/(:any)']	    = '$1/admin/$2';
$route['admin/(login|logout|remove_installer_directory)']			    = 'admin/$1';
$route['admin/([a-zA-Z0-9_-]+)']            = '$1/admin/index';

$route['api/ajax/(:any)']          			= 'api/ajax/$1';
$route['api/([a-zA-Z0-9_-]+)/(:any)']	    = '$1/api/$2';
$route['api/([a-zA-Z0-9_-]+)']              = '$1/api/index';

$route['register']                          = 'users/register';
$route['user/(:any)']	                    = 'users/view/$1';
$route['my-profile']	                    = 'users/index';
$route['edit-profile']	                    = 'users/edit';

$route['sitemap.xml']                       = 'sitemap/xml';

$route['contact']                           = 'sr_contact/index';

$route['profile']                           = 'sr_users/profile';
$route['me']                           = 'sr_users/profile';
$route['dashboard']                           = 'sr_users/dashboard';
$route['signup']                           = 'sr_users/register';
$route['signup/personal']                           = 'sr_users/personal_register';
$route['signup/company']                           = 'sr_users/company_register';
$route['login']                           = 'sr_users/login';
$route['logout']                           = 'sr_users/logout';
$route['ajaxload']                           = 'sr_science/sr_ajax/load';
$route['activate/(:num)/(:any)/(:any)']                           = 'sr_users/activate/$1/$2';
$route['view-article']                  = 'catalog/article/index';
$route['add-article']                  = 'catalog/article/add';
/**
 * Custom url from url rewrite table
 * @author datnguyen.cntt@gmail.com
 */
/* End of file routes.php */
/*$ci = &get_instance();
$ci->load->helper('virgo');
if(is_cache_file_support()){
    die('=============');
}*/
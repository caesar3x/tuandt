<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$route['category/admin(:any)?']		= 'admin/category$1';
$route['categorys']					= 'category/index';
$route['category(/:any)?']			= 'category/view$1';

/* End of file routes.php */


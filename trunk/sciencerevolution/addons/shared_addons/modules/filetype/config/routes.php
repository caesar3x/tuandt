<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$route['filetype/admin(:any)?']		= 'admin/filetype$1';
$route['filetypes']					= 'filetype/index';
$route['filetype(/:any)?']			= 'filetype/view$1';

/* End of file routes.php */


<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$route['languagelevel/admin(:any)?']		= 'admin/languagelevel$1';
$route['languagelevels']					= 'languagelevel/index';
$route['languagelevel(/:any)?']			= 'languagelevel/view$1';

/* End of file routes.php */


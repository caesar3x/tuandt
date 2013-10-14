<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$route['publicationtype/admin(:any)?']		= 'admin/publicationtype$1';
$route['publicationtypes']					= 'publicationtype/index';
$route['publicationtype(/:any)?']			= 'publicationtype/view$1';

/* End of file routes.php */


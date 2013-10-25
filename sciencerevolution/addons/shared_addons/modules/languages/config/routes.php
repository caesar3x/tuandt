<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/18/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
$route['languages/index'] = 'languages/index';

$route['admin/languages/(:any)'] = "index.php/admin/languages/index$1";
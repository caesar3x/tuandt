<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/11/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
// front-end
$route['default_controller']                = 'sr_homepage';
/*$route['(science)(/)'] = 'science/index';*/
$route['(sr_homepage)(/)']			                    = 'sr_homepage/index';
/*$route['science(/:num)?']			                    = 'science/num$1';
$route['scienced/test?']			                    = 'science/check';
$route['scienced/taxo(/:any)?']                                   =   'science/taxo$1';*/
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/3/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
$route['default_controller']                = 'soslaundry';
$route['soslaundry(/:num)?']			= 'soslaundry/index$1';
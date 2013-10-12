<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/11/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
// front-end
$route['science(/)?']			                    = 'science/index$1';
$route['science(/:num)?']			                    = 'science/num$1';
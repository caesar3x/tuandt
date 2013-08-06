<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/6/13
 */
defined('BASEPATH') OR exit('No direct script access allowed.');
if (!function_exists("debug")) {

    function debug($var, $exit = false) {
        echo "\n<pre>";

        if (is_array($var) || is_object($var)) {
            echo htmlentities(print_r($var, true));
        }
        elseif (is_string($var)) {
            echo "string(" . strlen($var) . ") \"" . htmlentities($var) . "\"\n";
        }
        else {
            var_dump($var);
        }
        echo "</pre>";

        if ($exit) {
            exit;
        }
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/6/13
 */
defined('BASEPATH') OR exit('No direct script access allowed.');
if (!function_exists('genRandomString'))
{
    function genRandomString($length = 100) {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz _";
        $string = "";
        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters)-1)];
        }
        return $string;
    }
}
if (!function_exists('getHotelName'))
{
    function getHotelName($id){
        $CI = get_instance();
        $CI->load->model('hotel_m');
        $row = $CI->hotel_m->get($id);
        return $row;
    }
}

if (!function_exists('test'))
{
    function test() {
        $CI = get_instance();
        $CI->load->model('winner_m');
        $all_emails = $CI->winner_m->getAllEmail();
        return $all_emails;
    }
}
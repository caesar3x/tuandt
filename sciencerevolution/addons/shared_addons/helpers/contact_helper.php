<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/20/13
 */
defined('BASEPATH') OR exit('No direct script access allowed.');
if(!function_exists('get_dropdown_format_reasons')){
    function get_dropdown_format_reasons()
    {
        $ci = & get_instance();
        $ci->load->model('locations/sr_contact_reasons_m');
        $rowset = $ci->sr_contact_reasons_m->get_all();
        $data = array();
        if(!empty($rowset)){
            foreach($rowset as $row){
                $data[$row->id] = $row->name;
            }
        }
        return $data;
    }
}
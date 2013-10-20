<?php
/**
 * Locations Helper
 * All location functions
 * Created by Nguyen Tien Dat.
 * Date: 10/18/13
 */
defined('BASEPATH') OR exit('No direct script access allowed.');
/**
 * Location
 */
if(!function_exists('get_countries')){
    function get_countries()
    {
        $rowset = _get_cache('countries');
        if($rowset === false){
            $ci = & get_instance();
            $ci->load->model('locations/sr_countries_m');
            $rowset = $ci->sr_countries_m->get_all();
        }
        return $rowset;
    }
}
/**
 * Get all countries dropdown format
 */
if(!function_exists('get_dropdown_format_countries')){
    function get_dropdown_format_countries()
    {
        get_instance()->load->helper('locations');
        $rowset = get_countries();
        $data = array();
        if(!empty($rowset)){
            foreach($rowset as $row){
                $data[$row->country_code] = $row->country_name;
            }
        }
        return $data;
    }
}
/**
 * Get all states of country
 */
if(!function_exists('get_states_by_country')){
    function get_states_by_country($country)
    {
        if(!$country){
            return null;
        }
        $rowset = _get_cache('states_in_'.$country);
        if($rowset === false){
            $ci = & get_instance();
            $ci->load->model('locations/sr_states_m');
            $rowset = $ci->sr_states_m->get_by_country($country);
            _set_cache('states_in_'.$country,$rowset);
        }
        return $rowset;
    }
}
/**
 * Get dropdown format data of state
 */
if(!function_exists('get_dropdown_format_states_by_country')){
    function get_dropdown_format_states_by_country($country)
    {
        if(!$country){
            return null;
        }
        $rowset = get_states_by_country($country);
        $data = array();
        if(!empty($rowset)){
            foreach($rowset as $row){
                $data[$row->state_code] = $row->state_name;
            }
        }
        ksort($data);
        return $data;
    }
}
/**
 * Get all cities in state in country
 */
if(!function_exists('get_cities_by_state_and_country')){
    function get_cities_by_state_and_country($country,$state)
    {
        $rowset = _get_cache('cities_in_'.$state.'_in_'.$country);
        if($rowset === false){
            $ci = & get_instance();
            $ci->load->model('locations/sr_cities_m');
            $rowset = $ci->sr_cities_m->get_by_state_and_country($country,$state);
            _set_cache('states_in_'.$country,$rowset);
        }
        return $rowset;
    }
}
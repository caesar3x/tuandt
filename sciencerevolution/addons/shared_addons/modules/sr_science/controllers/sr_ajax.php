<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/18/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_ajax extends Standard_Controller
{
    public function __construct()
    {
        parent::__construct();

    }
    public function load()
    {
        $country = $this->input->get('country');
        $state  = $this->input->get('state');
        if(!empty($country) && !empty($state)){
            echo $this->getHtmlCities($country,$state);
        }elseif(!empty($country) && is_string($country)){
            echo $this->getHtmlStatesByCountry($country);
        }
        $this->template->set_layout(false);
    }
    private function getHtmlCities($country,$state)
    {
        $cities = get_cities_by_state_and_country($country,$state);
        $html = '';
        if(!empty($cities)){
            foreach($cities as $city){
                $html .= '<option selected value="'.$city->city_name.'">'.$city->city_name.'</option>';
            }
        }
        return $html;
    }
    /**
     * Get html format of states
     * @param $country
     * @return string
     */
    private function getHtmlStatesByCountry($country)
    {
        $states = get_dropdown_format_states_by_country($country);
        $html = '';
        if(!empty($states)){
            foreach($states as $key=>$state){
                $html .= '<option selected value="'.$key.'">'.$state.'</option>';
            }
        }
        return $html;
    }
}
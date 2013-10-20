<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/18/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_cities_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'sr_cities';
    }
    public function get_by_state_and_country($country,$state)
    {
        if(!$country || !$state){
            return null;
        }
        $this->db->where('country_code',$country);
        $this->db->where('state_code',$state);
        $this->db->order_by('city_name','ASC');
        $query = $this->db->get($this->_table);
        $rowset = $query->result();
        return $rowset;
    }
}
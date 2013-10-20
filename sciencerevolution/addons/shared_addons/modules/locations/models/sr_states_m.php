<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/18/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_states_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'sr_states';
    }

    /**
     * Get by country code
     * @param $country
     * @return mixed
     */
    public function get_by_country($country)
    {
        if(!$country){
            return null;
        }
        $this->db->where('country_code',$country);
        $this->db->order_by('country_code','ASC');
        $query = $this->db->get('sr_states');
        $rowset = $query->result();
        return  $rowset;
    }
}
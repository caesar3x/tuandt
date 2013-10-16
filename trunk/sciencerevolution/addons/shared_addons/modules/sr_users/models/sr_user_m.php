<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/13/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_user_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'sr_user';
    }
    public function create($input)
    {
        return $this->db->insert($this->_table, $input);
    }
    public function get_all_active_users()
    {
        $query = $this->db->where('status','activated')->get($this->_table);;
        $rowset = $query->result();
        $data = new stdClass();
        if(!empty($rowset)){
            foreach($rowset as $row){
                $data->{$row->id} = $row;
            }
        }
        return $data;
    }
    public function get_user($id)
    {
        if(!$id || !is_numeric($id)){
            return null;
        }
        return $this->get_by('id',$id);
    }
}
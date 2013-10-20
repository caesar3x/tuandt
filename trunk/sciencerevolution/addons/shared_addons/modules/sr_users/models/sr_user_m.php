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

    /**
     * GEt user by id
     * @param $id
     * @return null|object
     */
    public function get_user($id)
    {
        if(!$id || !is_numeric($id)){
            return null;
        }
        return $this->get_by('id',$id);
    }

    /**
     * @param $username
     * @return null|object
     */
    public function get_user_by_username($username)
    {
        if(!$username){
            return null;
        }
        return $this->get_by('username',$username);
    }
    /**
     * Count user have username
     * @param $username
     * @return mixed
     */
    public function count_by_username($username)
    {
        $this->db->where('username', $username);
        return $this->db->count_all_results($this->_table);
    }

    /**
     * Get by profile id
     * @param $profile_id
     * @return object
     */
    public function get_by_profile($profile_id)
    {
        $row = $this->get_by('profile_id',$profile_id);
        return $row;
    }

    /**
     * @param $user_id
     * @param $status
     * @return bool
     */
    public function update_status($user_id,$status)
    {
        return $this->update($user_id,array('status' => $status));
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/20/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_contacts_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'sr_contacts';
    }
    public function create($input)
    {
        if(empty($input)){
            return false;
        }
        $data = array(
            'name' => $input['full_name'],
            'phone_number' => $input['phone_number'],
            'email_address' => $input['email'],
            'website' => $input['website'],
            'message' => $input['message'],
            'reason_id' => $input['reason'],
            'created_on' => time()
        );
        $id = $this->insert($data);
        return ($id > 0);
    }
}
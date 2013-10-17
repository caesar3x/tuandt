<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/18/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_private_messages_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'sr_private_messages';
    }
    public function create($input)
    {
        return $this->db->insert($this->_table, $input);
    }
}
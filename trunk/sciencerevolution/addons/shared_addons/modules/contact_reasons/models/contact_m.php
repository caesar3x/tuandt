<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Contact_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'sr_contact_reasons';
    }

    public function create($input)
    {
        $to_insert = array(
            'name' => $input['name']
        );

        return $this->db->insert('sr_contact_reasons', $to_insert);
    }

}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Languages_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'sr_languages';
    }

    public function create($input)
    {
        $to_insert = array(
            'code' => $input['code'],
            'name' => $input['name'],
            'flag' => $input['flag']
        );

        return $this->db->insert('sr_languages', $to_insert);
    }
}
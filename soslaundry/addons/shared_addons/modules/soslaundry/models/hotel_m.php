<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/4/13
 */
?>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Hotel_m extends MY_Model {

    public function __construct()
    {
        parent::__construct();
        $this->_table = 'hotel';
    }

    //create a new item
    public function create($input)
    {
        $to_insert = array(
            'name' => $input['name'],
            'address' => $input['address'],
            'city' => $input['city']
        );

        return $this->db->insert('hotel', $to_insert);
    }
}

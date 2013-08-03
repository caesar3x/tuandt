<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/3/13
 */
?>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Winner_m extends MY_Model {

    public function __construct()
    {
        parent::__construct();

        /**
         * If the sample module's table was named "samples"
         * then MY_Model would find it automatically. Since
         * I named it "sample" then we just set the name here.
         */
        $this->_table = 'winner';
    }

    //create a new item
    public function create($input)
    {
        $to_insert = array(
            'name' => $input['name'],
            'phone' => $input['phone'],
            'email' => $input['email'],
            'register_on' => time(),
            'is_winner' => 0,
            'winner_on' => 0,
            'slug' => ''
        );

        return $this->db->insert('winner', $to_insert);
    }
}

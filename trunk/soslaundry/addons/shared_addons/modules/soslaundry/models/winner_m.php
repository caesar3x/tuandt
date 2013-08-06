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
        $this->_table = 'winner';
    }

    //create a new item
    public function create($input)
    {
        $to_insert = array(
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'phone' => $input['phone'],
            'email' => $input['email'],
            'hotel' => $input['hotel'],
            'register_on' => time(),
            'is_winner' => 0,
            'winner_on' => 0
        );
        return $this->db->insert('winner', $to_insert);
    }

    /**
     * Get All Email from Database
     * @return array
     */
    public function getAllEmail()
    {
        $emails = array();
        $data = $this->get_all();
        if(!empty($data)){
            foreach($data as $item){
                $emails[] = $item->email;
            }
        }
        return $emails;
    }
}

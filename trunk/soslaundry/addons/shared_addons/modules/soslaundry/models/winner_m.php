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

    /**
     * Get random winner with limit
     * @param $limit
     * @return mixed
     */
    public function get_random_winner($limit)
    {
        $this->db->where('is_winner','0');
        $this->db->order_by('id', 'RANDOM');
        $this->db->limit($limit);
        $query = $this->db->get('winner');
        return $query->result_array();

    }
    public function get_winner_today_data()
    {
        $data = $this->get_all();
        $winner = array();
        if(!empty($data)){
            foreach($data as $item){
                if((int)$item->is_winner == 1 && date('Ymd',time()) == date('Ymd',(int)$item->winner_on)){
                    $winner[] = $item;
                }
            }
        }
        return $winner;
    }
    public function get_winner_yesterday_data()
    {
        $data = $this->get_all();
        $winner = array();
        if(!empty($data)){
            foreach($data as $item){
                if((int)$item->is_winner == 1 && date('Ymd',strtotime("-1 day")) == date('Ymd',(int)$item->winner_on)){
                    $winner[] = $item;
                }
            }
        }
        return $winner;
    }
}

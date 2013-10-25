<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class News_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'sr_news';
    }

    public function create($input)
    {
        $to_insert = array(
            'title' => $input['title'],
            'sub_header' => $input['sub_header'],
            'thumbnail' => $input['thumbnail'],
            'content' => $input['content']
        );

        return $this->db->insert('sr_news', $to_insert);
    }
}
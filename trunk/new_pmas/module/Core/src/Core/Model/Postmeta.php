<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/16/13
 */
namespace Core\Model;

class Postmeta
{
    public $meta_id;
    public $meta_key;
    public $post_id;
    public $meta_value;

    public function exchangeArray($data)
    {
        $this->meta_id     = (isset($data['meta_id'])) ? $data['meta_id'] : 0;
        $this->meta_key = (isset($data['meta_key'])) ? $data['meta_key'] : null;
        $this->post_id  = (isset($data['post_id'])) ? $data['post_id'] : null;
        $this->meta_value  = (isset($data['meta_value'])) ? $data['meta_value'] : null;
    }
}
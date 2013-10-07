<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/7/13
 */
namespace Core\Model;

class Usermeta
{
    public $meta_id;
    public $meta_key;
    public $user_id;
    public $meta_value;
    public $note;
    public $time;
    public $deleted;
    public function exchangeArray($data)
    {
        $this->meta_id     = (isset($data['meta_id'])) ? (int) $data['meta_id'] : 0;
        $this->meta_key = (isset($data['meta_key'])) ? $data['meta_key'] : null;
        $this->user_id  = (isset($data['user_id'])) ? (int) $data['user_id'] : null;
        $this->meta_value  = (isset($data['meta_value'])) ? $data['meta_value'] : null;
        $this->note  = (isset($data['note'])) ? $data['note'] : null;
        $this->time  = (isset($data['time'])) ? (int) $data['time'] : time();
        $this->deleted  = (isset($data['deleted'])) ? (int) $data['deleted'] : 0;
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

class DeviceCondition
{
    public $condition_id;
    public $name;
    public $recycler;
    public $deleted;

    public function exchangeArray($data)
    {
        $this->condition_id     = (isset($data['condition_id'])) ? $data['condition_id'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->recycler     = (isset($data['recycler'])) ? $data['recycler'] : 'tdm';
        $this->deleted     = (isset($data['deleted'])) ? $data['deleted'] : 0;
    }
}
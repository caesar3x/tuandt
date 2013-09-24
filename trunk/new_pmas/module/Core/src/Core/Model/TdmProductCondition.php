<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

class TdmProductCondition
{
    public $condition_id;
    public $name;
    public $deleted;

    public function exchangeArray($data)
    {
        $this->condition_id     = (isset($data['condition_id'])) ? $data['condition_id'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->deleted     = (isset($data['deleted'])) ? $data['deleted'] : 0;
    }
}
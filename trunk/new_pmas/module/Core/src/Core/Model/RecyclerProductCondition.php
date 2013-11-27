<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/20/13
 */
namespace Core\Model;

class RecyclerProductCondition
{
    public $condition_id;
    public $name;

    public function exchangeArray($data)
    {
        $this->condition_id     = (isset($data['condition_id'])) ? $data['condition_id'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
    }
}
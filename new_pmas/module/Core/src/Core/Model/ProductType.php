<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\Model;

class ProductType
{
    public $type_id;
    public $name;
    public $deleted;

    public function exchangeArray($data)
    {
        $this->type_id     = (isset($data['type_id'])) ? $data['type_id'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->deleted     = (isset($data['deleted'])) ? $data['deleted'] : 0;
    }
}
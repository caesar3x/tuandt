<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

class Brand
{
    public $brand_id;
    public $name;

    public function exchangeArray($data)
    {
        $this->brand_id     = (isset($data['brand_id'])) ? $data['brand_id'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
    }
}
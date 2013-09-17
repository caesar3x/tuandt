<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

class Device
{
    public $device_id;
    public $name;
    public $brand;
    public $model;
    public $type_id;
    public $price;
    public $condition_id;
    public $currency;
    public function exchangeArray($data)
    {
        $this->device_id     = (isset($data['device_id'])) ? $data['device_id'] : 0;
        $this->currency     = (isset($data['currency'])) ? $data['currency'] : 'HKD';
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->brand     = (isset($data['brand'])) ? $data['brand'] : null;
        $this->model     = (isset($data['model'])) ? $data['model'] : null;
        $this->price     = (isset($data['price'])) ? $data['price'] : 0;
        $this->condition_id     = (isset($data['condition_id'])) ? $data['condition_id'] : 0;
        $this->type_id     = (isset($data['type_id'])) ? $data['type_id'] : 0;
    }
}
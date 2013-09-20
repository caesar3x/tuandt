<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

class Device
{
    public $device_id;
    public $brand;
    public $model;
    public $type_id;
    public $deleted;

    public function exchangeArray($data)
    {
        $this->device_id     = (isset($data['device_id'])) ? $data['device_id'] : 0;
        $this->brand     = (isset($data['brand'])) ? $data['brand'] : null;
        $this->model     = (isset($data['model'])) ? $data['model'] : null;
        $this->type_id     = (isset($data['type_id'])) ? $data['type_id'] : 0;
        $this->deleted     = (isset($data['deleted'])) ? $data['deleted'] : 0;
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

class RecyclerDevice
{
    public $device_id;
    public $currency;
    public $price;
    public $name;
    public $condition_id;
    public $recycler_id;
    public $exchange_price;

    public function exchangeArray($data)
    {
        $this->device_id     = (isset($data['device_id'])) ? $data['device_id'] : 0;
        $this->currency     = (isset($data['currency'])) ? $data['currency'] : 'HKD';
        $this->price     = (isset($data['price'])) ? $data['price'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->condition_id     = (isset($data['condition_id'])) ? $data['condition_id'] : 0;
        $this->recycler_id     = (isset($data['recycler_id'])) ? $data['recycler_id'] : 0;
        $this->exchange_price     = (isset($data['exchange_price'])) ? $data['exchange_price'] : 0;
    }
}
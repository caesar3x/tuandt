<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/20/13
 */
namespace Core\Model;

class Exchange
{
    public $currency;
    public $country_id;
    public $exchange_rate;
    public $time;
    public function exchangeArray($data)
    {
        $this->currency     = (isset($data['currency'])) ? $data['currency'] : null;
        $this->country_id     = (isset($data['country_id'])) ? (int) $data['country_id'] : 0;
        $this->exchange_rate     = (isset($data['exchange_rate'])) ? (float)$data['exchange_rate'] : 0;
        $this->time     = (isset($data['time'])) ? $data['time'] : time();
    }
}
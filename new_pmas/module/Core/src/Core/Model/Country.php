<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\Model;

class Country
{
    public $country_id;
    public $name;
    public $currency;
    public $symbol;
    public $position;
    public $deleted;

    public function exchangeArray($data)
    {
        $this->country_id     = (isset($data['country_id'])) ? $data['country_id'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->currency     = (isset($data['currency'])) ? $data['currency'] : null;
        $this->symbol     = (isset($data['symbol'])) ? $data['symbol'] : '$';
        $this->position     = (isset($data['position'])) ? $data['position'] : 'before';
        $this->deleted     = (isset($data['deleted'])) ? $data['deleted'] : 0;
    }
}
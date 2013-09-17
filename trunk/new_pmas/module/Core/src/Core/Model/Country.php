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
    public $deleted;

    public function exchangeArray($data)
    {
        $this->country_id     = (isset($data['country_id'])) ? $data['country_id'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->deleted     = (isset($data['deleted'])) ? $data['deleted'] : 0;
    }
}
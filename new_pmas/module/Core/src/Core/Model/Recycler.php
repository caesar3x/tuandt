<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\Model;

class Recycler
{
    public $recycler_id;
    public $name;
    public $country_id;
    public $email;
    public $website;
    public $telephone;
    public $address;

    public function exchangeArray($data)
    {
        $this->recycler_id     = (isset($data['recycler_id'])) ? $data['recycler_id'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->country_id     = (isset($data['country_id'])) ? $data['country_id'] : null;
        $this->email     = (isset($data['email'])) ? $data['email'] : null;
        $this->website     = (isset($data['website'])) ? $data['website'] : null;
        $this->telephone     = (isset($data['telephone'])) ? $data['telephone'] : null;
        $this->address     = (isset($data['address'])) ? $data['address'] : null;
    }
}
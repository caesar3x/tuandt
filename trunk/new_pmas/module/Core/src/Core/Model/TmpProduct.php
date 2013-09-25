<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/22/13
 */
namespace Core\Model;

class TmpProduct
{
    public $id;
    public $country_id;
    public $price;
    public $name;
    public $condition_id;
    public $currency;
    public $brand_id;
    public $model;
    public $type_id;
    public $recycler_id;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : 0;
        $this->recycler_id     = (isset($data['recycler_id'])) ? $data['recycler_id'] : 0;
        $this->country_id     = (isset($data['country_id'])) ? $data['country_id'] : 0;
        $this->price     = (isset($data['price'])) ? $data['price'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->condition_id     = (isset($data['condition_id'])) ? $data['condition_id'] : 0;
        $this->currency     = (isset($data['currency'])) ? $data['currency'] : null;
        $this->brand_id     = (isset($data['brand_id'])) ? $data['brand_id'] : null;
        $this->model     = (isset($data['model'])) ? $data['model'] : null;
        $this->type_id     = (isset($data['type_id'])) ? $data['type_id'] : 0;
    }
}
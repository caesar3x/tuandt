<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

class TdmProduct
{
    public $product_id;
    public $country_id;
    /*public $price;*/
    public $name;
    public $model;
    public $type_id;
    public $brand_id;
    public $condition_id;
    /*public $currency;*/
    public $deleted;
    public function exchangeArray($data)
    {
        $this->product_id     = (isset($data['product_id'])) ? $data['product_id'] : 0;
        $this->country_id     = (isset($data['country_id'])) ? $data['country_id'] : 0;
        /*$this->price     = (isset($data['price'])) ? $data['price'] : 0;*/
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->model     = (isset($data['model'])) ? $data['model'] : null;
        $this->type_id     = (isset($data['type_id'])) ? $data['type_id'] : 0;
        $this->brand_id     = (isset($data['brand_id'])) ? $data['brand_id'] : 0;
        $this->condition_id     = (isset($data['condition_id'])) ? $data['condition_id'] : 0;
        /*$this->currency     = (isset($data['currency'])) ? $data['currency'] : null;*/
        $this->deleted     = (isset($data['deleted'])) ? $data['deleted'] : 0;
    }
}
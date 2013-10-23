<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/23/13
 */
namespace Core\Soap;
class ComplexRecyclerProductTypeC
{
    public $product_id;
    public $price;
    public $name;
    public $model;
    public $type_id;
    public $brand_id;
    public $condition_id;
    public $popular;
    public $currency;
    public $deleted;
    public function exchangeArray($data)
    {
        $this->product_id     = (isset($data['product_id'])) ? $data['product_id'] : 0;
        $this->price     = (isset($data['price'])) ? $data['price'] : 0;
        $this->name     = (isset($data['product_name'])) ? $data['product_name'] : null;
        $this->model     = (isset($data['product_model'])) ? $data['product_model'] : null;
        $this->type_id     = (isset($data['type_id'])) ? $data['type_id'] : 0;
        $this->brand_id     = (isset($data['brand_id'])) ? $data['brand_id'] : 0;
        $this->condition_id     = (isset($data['condition_id'])) ? $data['condition_id'] : 0;
        $this->popular     = (isset($data['popular'])) ? (int)$data['popular'] : 0;
        $this->currency     = (isset($data['currency'])) ? $data['currency'] : 'HKD';
        $this->deleted     = (isset($data['deleted'])) ? $data['deleted'] : 0;
    }
    public function exchangeUpdateData($data)
    {
        $result = new \stdClass();
        $keys = array('type_id','brand_id','condition_id','model');
        if(isset($data['product_name'])){
            $result->name = $data['product_name'];
        }
        foreach($keys as $key){
            if(array_key_exists($key,$data)){
                $result->{$key} = $data[$key];
            }
        }
        return $result;
    }
}
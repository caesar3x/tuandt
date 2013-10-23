<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/22/13
 */
namespace Core\Soap;
class ComplexRecyclerProductType
{
    public $product_id;
    public $recycler_id;
    public $price;
    public $name;
    public $model;
    public $type_id;
    public $brand_id;
    public $condition_id;
    public $temp_id;
    public $currency;
    public $date;
    public $deleted;
    public function exchangeArray($data)
    {
        $time = time();
        if(isset($data['updated_at'])){
            $parse = \DateTime::createFromFormat('d-m-Y H:i:s',$data['updated_at']." 00:00:00");
            if($parse){
                $time = $parse->getTimestamp();
            }
        }
        $this->product_id     = (isset($data['product_id'])) ? $data['product_id'] : 0;
        $this->recycler_id     = (isset($data['recycler_id'])) ? $data['recycler_id'] : 0;
        $this->price     = (isset($data['base_price'])) ? $data['base_price'] : 0;
        $this->name     = (isset($data['product_name'])) ? $data['product_name'] : null;
        $this->model     = (isset($data['product_model'])) ? $data['product_model'] : null;
        $this->type_id     = (isset($data['type_id'])) ? $data['type_id'] : 0;
        $this->brand_id     = (isset($data['brand_id'])) ? $data['brand_id'] : 0;
        $this->condition_id     = (isset($data['condition_id'])) ? $data['condition_id'] : 0;
        $this->temp_id     = (isset($data['temp_id'])) ? $data['temp_id'] : 0;
        $this->date     = $time;
        $this->currency     = (isset($data['currency'])) ? $data['currency'] : null;
        $this->deleted     = (isset($data['deleted'])) ? $data['deleted'] : 0;
    }
}
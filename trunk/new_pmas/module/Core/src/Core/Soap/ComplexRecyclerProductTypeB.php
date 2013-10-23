<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/22/13
 */
namespace Core\Soap;

class ComplexRecyclerProductTypeB
{
    /**
     * Exchange Data to complex type
     * @param $data
     * @return \stdClass
     */
    public function exchangeArray($data)
    {
        $result = new \stdClass();
        $keys = array('recycler_id','type_id','brand_id','condition_id','currency');
        if(isset($data['product_name'])){
            $result->name = $data['product_name'];
        }
        if(isset($data['base_price'])){
            $result->price = $data['base_price'];
        }
        if(isset($data['updated_at'])){
            $parse = \DateTime::createFromFormat('d-m-Y H:i:s',$data['updated_at']." 00:00:00");
            if($parse){
                $time = $parse->getTimestamp();
                $result->date = $time;
            }
        }
        foreach($keys as $key){
            if(array_key_exists($key,$data)){
                $result->{$key} = $data[$key];
            }
        }
        return $result;
    }
}
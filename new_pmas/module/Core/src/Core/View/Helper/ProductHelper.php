<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/30/13
 */
namespace Core\View\Helper;

use Zend\Debug\Debug;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class ProductHelper extends AbstractHelper
{
    /**
     * @var $serviceLocator
     */
    protected $serviceLocator;

    /**
     * @param ServiceManager $serviceLocator
     */
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function getCountryHelper()
    {
        return $this->serviceLocator->get('viewhelpermanager')->get('country');
    }
    /**
     * @param $product_id
     * @return null
     */
    public function getRecyclerProductName($product_id)
    {
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        if(!$product_id || $product_id == 0){
            return null;
        }
        $entry = $recyclerProductTable->getEntry($product_id);
        if(empty($entry)){
            return null;
        }
        return $entry->name;
    }
    public function getRecyclerProductDate($product_id)
    {
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        if(!$product_id || $product_id == 0){
            return null;
        }
        $entry = $recyclerProductTable->getEntry($product_id);
        if(empty($entry)){
            return null;
        }
        return $entry->date;
    }
    public function getRecyclerProductModel($product_id)
    {
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        if(!$product_id || $product_id == 0){
            return null;
        }
        $entry = $recyclerProductTable->getEntry($product_id);
        if(empty($entry)){
            return null;
        }
        return $entry->model;
    }

    /**
     * @param $product_id
     * @return null
     */
    public function getRecyclerProductCurrency($product_id)
    {
        if($product_id == null || $product_id == 0){
            return null;
        }
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        $entry = $recyclerProductTable->getEntry($product_id);
        if(!empty($entry)){
            return $entry->currency;
        }
        return null;
    }

    /**
     * @param $product_id
     * @return null
     */
    public function getTdmProductCurrency($product_id)
    {
        if($product_id == null || $product_id == 0){
            return null;
        }
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        $entry = $tdmProductTable->getEntry($product_id);
        if(!empty($entry)){
            return $entry->currency;
        }
        return null;
    }
    public function getRecyclerProductPrice($product_id)
    {
        if($product_id == null || $product_id == 0){
            return null;
        }
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        $entry = $recyclerProductTable->getEntry($product_id);
        if(!empty($entry)){
            return  (float) $entry->price;
        }
        return null;
    }

    /**
     * @param $product_id
     * @return float|null
     */
    public function getTdmProductPrice($product_id)
    {
        if($product_id == null || $product_id == 0){
            return null;
        }
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        $entry = $tdmProductTable->getEntry($product_id);
        if(!empty($entry)){
            return  (float) $entry->price;
        }
        return null;
    }
    public function getRecyclerProductCondition($product_id)
    {
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        if(!$product_id || $product_id == 0){
            return null;
        }
        $entry = $recyclerProductTable->getEntry($product_id);
        if(empty($entry)){
            return null;
        }
        $condition_id = $entry->condition_id;
        if(!$condition_id || (int)$condition_id == 0){
            return null;
        }
        $conditionTable = $this->serviceLocator->get('TdmProductConditionTable');
        $conditionEntry = $conditionTable->getEntry($condition_id);
        if(empty($conditionEntry)){
            return null;
        }
        return $conditionEntry->name;
    }

    /**
     * @param $productsCurrency
     * @param $start
     * @param $end
     * @return array
     */
    public function getHighestPrice($productsCurrency,$start,$end)
    {
        $exchangeTable = $this->serviceLocator->get('ExchangeRateTable');
        $productsExchangePrice = array();
        $productsExchangeDate = array();
        $productsExchangeRate = array();
        $highest = array();
        if(!empty($productsCurrency)){
            foreach($productsCurrency as $product_id=>$val){
                /**
                 * Get echange rate in time range
                 */
                $rowset = $exchangeTable->getHighestExchangeByCurrency($val['currency'],$start,$end);
                if(!empty($rowset)){
                    $productsExchangePrice[$product_id] = ((float)$val['price'])*((float)$rowset->exchange_rate);
                    $productsExchangeDate[$product_id] = $rowset->time;
                    $productsExchangeRate[$product_id] = $rowset->exchange_rate;
                }else{
                    $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$start);
                    if(!empty($currentExchange)){
                        $productsExchangePrice[$product_id] = ((float)$val['price'])*((float)$currentExchange->exchange_rate);
                        $productsExchangeDate[$product_id] = $currentExchange->time;
                        $productsExchangeRate[$product_id] = $currentExchange->exchange_rate;
                    }else{
                        $productsExchangePrice[$product_id] = (float)$val['price'];
                        $productsExchangeDate[$product_id] = null;
                        $productsExchangeRate[$product_id] = null;
                    }
                }
            }
            arsort($productsExchangePrice);
            if(!empty($productsExchangePrice)){
                foreach($productsExchangePrice as $product_id=>$price){
                    $highest = array(
                        'product_id' => $product_id,
                        'exchange_price' => $price,
                        'price' => $productsCurrency[$product_id]['price'],
                        'currency' => $productsCurrency[$product_id]['currency'],
                        'time' => $this->getRecyclerProductDate($product_id),
                        'exchange_rate' => $productsExchangeRate[$product_id],
                        'recycler_id' => $productsCurrency[$product_id]['recycler_id']
                    );
                    break;
                }
            }
        }
        return $highest;
    }

    /**
     * @return mixed
     */
    public function getAllTdmProducts()
    {
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        $products = $tdmProductTable->getAvaiableRows();
        return $products;
    }

    /**
     * @param $model
     * @param $condition_id
     * @param int $limit
     * @return mixed
     */
    public function getRecyclerProductsByModel($model,$condition_id,$limit = 3)
    {
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        $rowset = $recyclerProductTable->getProductsByModel($model,$condition_id,$limit);
        return $rowset;
    }

    /**
     * @param $model
     * @param $condition_id
     * @param int $limit
     * @return mixed
     */
    public function getTopPriceRecyclerProductByModel($model,$condition_id,$limit = 3)
    {
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        $rowset = $recyclerProductTable->getTopPriceProductsByModel($model,$condition_id,$limit);
        return $rowset;
    }
    /**
     * @return array
     */
    public function getAllModelNames()
    {
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        $tdmProducts = $tdmProductTable->getAvaiableRows();
        $recyclerProducts = $recyclerProductTable->getAvaiableRows();
        $data = array();
        if(!empty($tdmProducts)){
            foreach($tdmProducts as $tp){
                $data[] = $tp->model;
            }
        }
        if(!empty($recyclerProducts)){
            foreach($recyclerProducts as $rp){
                $data[] = $rp->model;
            }
        }
        return array_unique($data);
    }
    public function getExchangeHelper()
    {
        return $this->serviceLocator->get('viewhelpermanager')->get('exchange');
    }
    public function filterHigherProduct($recycler_product,$higher = 50)
    {
        if(!$recycler_product){
            return null;
        }
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        $tdmProductsWithSameModel = $tdmProductTable->getRowByModel($recycler_product->model,$recycler_product->condition_id);
        if(empty($tdmProductsWithSameModel)){
            return null;
        }
        $tdmCurrentExchange = $this->getExchangeHelper()->getCurrentExchangeOfCurrency($tdmProductsWithSameModel->currency,time());
        $recyclerCurrentExchange = $this->getExchangeHelper()->getCurrentExchangeOfCurrency($recycler_product->currency,time());
        $recyclerExchangePrice = (float) $recycler_product->price*$recyclerCurrentExchange;
        $tdmExchangePrice = (float) $tdmProductsWithSameModel->price*$tdmCurrentExchange;
        if($tdmExchangePrice){
            $percentage = (($recyclerExchangePrice - $tdmExchangePrice)/$tdmExchangePrice)*100;
            if($percentage > $higher)
            {
                return array('percentage' => $percentage,'tdmExchangePrice' => $tdmExchangePrice);
            }
            return null;
        }
        return null;
    }
    public function filterCountryProduct($recycler_product)
    {
        if(!$recycler_product){
            return null;
        }
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        $tdmProductsWithSameModel = $tdmProductTable->getRowByModel($recycler_product->model,$recycler_product->condition_id);
        if(empty($tdmProductsWithSameModel)){
            return array('percentage' => 'N/A','tdmExchangePrice' => 'N/A');
        }
        $tdmCurrentExchange = $this->getExchangeHelper()->getCurrentExchangeOfCurrency($tdmProductsWithSameModel->currency,time());
        $recyclerCurrentExchange = $this->getExchangeHelper()->getCurrentExchangeOfCurrency($recycler_product->currency,time());
        $recyclerExchangePrice = (float) $recycler_product->price*$recyclerCurrentExchange;
        $tdmExchangePrice = (float) $tdmProductsWithSameModel->price*$tdmCurrentExchange;
        if($tdmExchangePrice){
            $percentage = (($recyclerExchangePrice - $tdmExchangePrice)/$tdmExchangePrice)*100;
            return array('percentage' => $percentage,'tdmExchangePrice' => $tdmExchangePrice);
        }else{
            return array('percentage' => 'N/A','tdmExchangePrice' => 'N/A');
        }
        return null;
    }
    /**
     * @return mixed
     */
    public function getAvailableCountries()
    {
        return $this->getCountryHelper()->getAvailableCountries();
    }

    /**
     * @param $country_id
     * @return mixed
     */
    public function getProductsByCountry($country_id)
    {
        $countryTable = $this->serviceLocator->get('RecyclerProductTable');
        return $countryTable->getProductsByCountry($country_id);
    }

    /**
     * @param $country_id
     * @param $model
     * @param $condition
     * @return mixed
     */
    public function getProductsByCountryAndModelAndCondition($country_id,$model,$condition)
    {
        $countryTable = $this->serviceLocator->get('RecyclerProductTable');
        return $countryTable->getProductsByCountryAndModelAndCondition($country_id,$model,$condition);
    }
}
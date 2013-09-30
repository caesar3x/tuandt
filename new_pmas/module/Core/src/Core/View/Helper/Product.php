<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/30/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class Product extends AbstractHelper
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
        $conditionTable = $this->serviceLocator->get('RecyclerProductConditionTable');
        $conditionEntry = $conditionTable->getEntry($condition_id);
        if(empty($conditionEntry)){
            return null;
        }
        return $conditionEntry->name;
    }
}
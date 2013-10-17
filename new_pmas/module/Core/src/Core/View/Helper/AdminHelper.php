<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/8/13
 */
namespace Core\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;
class AdminHelper extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @return mixed
     */
    public function getLastExchange()
    {
        $exchangeTable = $this->serviceLocator->get('ExchangeRateTable');
        return $exchangeTable->getLastExchange();
    }

    /**
     * @return mixed
     */
    public function getLastTdmProducts()
    {
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        return $tdmProductTable->getLastProducts();
    }

    /**
     * @return mixed
     */
    public function getLastRecyclers()
    {
        $recyclerTable = $this->serviceLocator->get('RecyclerTable');
        return $recyclerTable->getLastRecyclers();
    }

    /**
     * @return mixed
     */
    public function getTotalRecycler()
    {
        $recyclerTable = $this->serviceLocator->get('RecyclerTable');
        return $recyclerTable->getTotalRows();
    }

    /**
     * @return mixed
     */
    public function getTotalTdmProduct()
    {
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        return $tdmProductTable->getTotalRows();
    }

    /**
     * @return mixed
     */
    public function getTotalRecyclerProduct()
    {
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        return $recyclerProductTable->getTotalRows();
    }
}
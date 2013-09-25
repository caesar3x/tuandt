<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/25/13
 */
namespace Core\View\Helper;

use Zend\Debug\Debug;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class Exchange extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return array
     */
    public function getAvailableCurrencies()
    {
        $exchangeTable = $this->serviceLocator->get('ExchangeRateTable');
        $data = array('none' => 'Select Currency');
        $currencies = $exchangeTable->getAvailableCurrencies();
        if(!empty($currencies)){
            $data = array_merge($data,$currencies);
        }
        return $data;
    }
}
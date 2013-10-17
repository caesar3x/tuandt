<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/25/13
 */
namespace Core\View\Helper;

use Zend\Debug\Debug;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class ExchangeHelper extends AbstractHelper
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

    /**
     * @param $currency
     * @param null $time
     * @return null
     */
    public function getCurrentExchangeOfCurrency($currency,$time = null)
    {
        $exchangeTable = $this->serviceLocator->get('ExchangeRateTable');
        $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($currency,$time);
        if(empty($currentExchange)){
            return 1;
        }
        return (float) $currentExchange->exchange_rate;
    }
    public function getExchangeByCurrency($currency = null,$start = null,$end = null)
    {
        $exchangeTable = $this->serviceLocator->get('ExchangeRateTable');
        $exchanges = $exchangeTable->getExchangeByCurrency($currency,$start,$end);
        return $exchanges;
    }
}
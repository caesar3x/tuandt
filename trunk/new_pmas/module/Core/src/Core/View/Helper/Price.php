<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/28/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class Price extends AbstractHelper
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
    public function format($price)
    {
        return number_format($price,2);
    }

    /**
     * @param $price
     * @param $currency
     * @return string
     */
    public function getExchange($price,$currency)
    {
        $exchangeTable = $this->serviceLocator->get('ExchangeRateTable');
        $lastExchange = $exchangeTable->getLastCurrencyExchange($currency);
        if(empty($lastExchange)){
            return $price;
        }
        $rate = $lastExchange->exchange_rate;
        $exchangePrice = $price*$rate;
        return $exchangePrice;
    }
    public function getPercent($price1,$price2)
    {
        $percent = $price1/$price2;
        $percent2 = $percent*100;
        return number_format($percent2,2);
    }
}
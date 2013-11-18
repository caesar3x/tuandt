<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/28/13
 */
namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager;

class PriceHelper extends CoreHelper
{
    public function __construct(ServiceManager $serviceLocator,Request $request)
    {
        parent::__construct($serviceLocator,$request);
    }
    public function format($price,$decimal = 2)
    {
        if(!is_numeric($price)){
            return $price;
        }
        return round($price,$decimal,PHP_ROUND_HALF_UP);
        /*return number_format($price,2);*/
    }

    /**
     * @param $price
     * @param $currency
     * @return int|string
     */
    public function formatCurrency($price,$currency)
    {
        if(!is_numeric($price) || !$currency || $currency == null){
            return $price;
        }
        $countryTable = $this->serviceLocator->get('CountryTable');
        $entry = $countryTable->getEntryByCurrency($currency);
        if(!empty($entry)){
            $symbol = $entry->symbol;
            $postion = $entry->position;
            if($postion == 'after'){
                return $this->format($price).$symbol;
            }else{
                return $symbol.$this->format($price);
            }
        }else{
            return $price;
        }
    }
    /**
     * @param $price
     * @param $currency
     * @return string
     */
    public function getExchange($price,$currency)
    {
        $current_exchange = $this->getViewHelper('exchange')->getCurrentExchangeOfCurrency($currency);
        /*$exchangeTable = $this->serviceLocator->get('ExchangeRateTable');
        $lastExchange = $exchangeTable->getLastCurrencyExchange($currency);
        if(empty($lastExchange)){
            return $price;
        }
        $rate = $lastExchange->exchange_rate;*/
        $exchangePrice = $price/$current_exchange;
        return $exchangePrice;
    }
    public function getPercent($price1,$price2)
    {
        if($price2 == 0 || $price2 == null){
            return null;
        }
        $percent = ($price1-$price2)/$price2;
        $percent2 = $percent*100;
        return $this->format($percent2);
        /*return round($percent2,2,PHP_ROUND_HALF_UP);*/
        /*return number_format($percent2,2);*/
    }
}
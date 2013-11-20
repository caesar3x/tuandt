<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 11/15/13
 */
namespace Application\Controller;

use Core\Controller\AbstractController;
use Zend\Debug\Debug;

class CronController extends AbstractController
{
    protected $_httpHeader;

    public function __construct()
    {
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                    "Cookie: foo=bar\r\n".
                    "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
            )
        );

        $http_header = stream_context_create($opts);
        $this->_httpHeader = $http_header;
    }
    public function indexAction()
    {
        parent::initnotauthAction();
        die;
        $rec = $this->getAllRecyclers('http://www.sellmymobile.com/phone/t-mobile-mda-vario-ii/');
        if(isset($rec['Top Dollar Mobile'])){
            unset($rec['Top Dollar Mobile']);
        }
        $data = array();
        Debug::dump($rec);
        $brandTable = $this->sm->get('BrandTable');
        $recyclerProductTable = $this->sm->get('RecyclerProductTable');
        $recyclerTable = $this->sm->get('RecyclerTable');
        if(!empty($rec)){
            foreach($rec as $rec_name=>$rec_price){
                $row = array();
                $rec_price = str_replace('£', '', $rec_price);
                $row['price'] = $rec_price;
                $row['currency'] = 'GBP';
                $row['name'] = '$phone_name';
                $row['model'] = '$phone_name';
                $row['recycler_id'] = $recyclerTable->getIdByName($rec_name);
                $row['condition_id'] = 6;
                $row['lastest'] = 1;
                $row['type_id'] = 3;
                $row['brand_id'] = $brandTable->getBrandIdByName('Từ từ');
                $this->log_debug(print_r($row,1));
                $data[] = $row;
                $recyclerProductTable->saveData($row);
            }
        }
        Debug::dump($data);
        die;
    }
    public function sellmymobileAction()
    {
        parent::initnotauthAction();
        $brands = $this->getAllBrands();
        $brandTable = $this->sm->get('BrandTable');
        $recyclerProductTable = $this->sm->get('RecyclerProductTable');
        $recyclerTable = $this->sm->get('RecyclerTable');
        foreach ($brands as $brand_name => $brand_url) {
            /*$this->log_debug('$brand_name : '.$brand_name);
            $this->log_debug('$brand_url : '.$brand_url);*/
            $phones = $this->getAllPhonesFromBrand($brand_url);
            /*$this->log_debug($phones);*/
            foreach ($phones as $phone_url => $phone_name) {
                $rec = $this->getAllRecyclers($phone_url);
                if(isset($rec['Top Dollar Mobile'])){
                    unset($rec['Top Dollar Mobile']);
                }
                /*Debug::dump($rec);*/
                if(!empty($rec)){
                    foreach($rec as $rec_name=>$rec_price){
                        $row = array();
                        $rec_price = str_replace('£', '', $rec_price);
                        $row['price'] = $rec_price;
                        $row['currency'] = 'GBP';
                        $row['name'] = $phone_name;
                        $row['model'] = $phone_name;
                        $row['recycler_id'] = $recyclerTable->getIdByName($rec_name);
                        $row['condition_id'] = 6;
                        $row['lastest'] = 1;
                        $row['type_id'] = 3;
                        $row['date'] = time();
                        $row['brand_id'] = $brandTable->getBrandIdByName($brand_name);
                        /*$this->log_debug(print_r($row,1));*/
                        /*$data[] = $row;*/
                        $recyclerProductTable->saveData($row);
                    }
                }
            }
        }
        $messages = $this->getMessages();
        $this->addSuccessFlashMessenger($this->__($messages['IMPORT_FROM_SELLYMOBILE_SUCCESS']));
        $this->redirectUrl('recycler');
    }
    public function getAllRecyclers($phone_url) { //returns dictionary [recycler_name => price] sorted by price for specific phone
        $page_source = file_get_contents($phone_url, false, $this->_httpHeader);
        $chunks = preg_split('/Complete List/', $page_source);
        preg_match_all('/<a class="trigger-comp merchant small.*?m2:\'(?P<name>.*?)\'.*?<td class="price" width="50%">(?P<price>.*?)<\/td>/s', $chunks[1], $rec_temp);
        //var_dump($rec_temp['name']);
        //var_dump($rec_temp['price']);
        $rec = array();
        for ($i = 0; $i < count($rec_temp['name']); $i += 1) {
            $rec[$rec_temp['name'][$i]] = $rec_temp['price'][$i];
        }
        natsort($rec);
        $rec = array_reverse($rec);
        //var_dump($rec);
        return $rec;
    }
    public function getAllPhonesFromBrand($brand_url) { //returns dictionary [phone_url => phone_name]
        $page_source = file_get_contents($brand_url, false, $this->_httpHeader);
        $chunks = preg_split('/<h1 class="clear">Search All/', $page_source);
        //var_dump($chunks);
        preg_match_all('/<li class="loading.*?<a href="(?P<url>.*?)" title="Compare prices for the (?P<name>.*?)"/s', $chunks[1], $phones_temp);
        $phones = array();
        for ($i = 0;  $i < count($phones_temp['url']); $i += 1) {
            $phones[trim($phones_temp['url'][$i])] = trim($phones_temp['name'][$i]);
        }
        //var_dump($phones);
        return $phones;
    }
    public function getAllBrands() { //returns dictionary [brand_name => url]

        $page_source = file_get_contents('http://www.sellmymobile.com/search/', false, $this->_httpHeader);

        preg_match_all('/<li class="loading.*?<a href="(?P<url>.*?)" title="View all (?P<name>.*?) handsets">/s', $page_source, $brands_temp);
        $brands = array();
        for ($i = 0;  $i < count($brands_temp['name']); $i += 1) {
            $brands[trim($brands_temp['name'][$i])] = trim($brands_temp['url'][$i]);
        }
        //var_dump($brands);
        return $brands;
    }
}
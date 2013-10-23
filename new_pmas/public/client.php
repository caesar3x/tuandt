<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/22/13
 */
require '../manual_loader.php';
$wsdl = "http://".$_SERVER['HTTP_HOST'].'/soap/tdm-product?wsdl';
$client = new \Zend\Soap\Client($wsdl);
$client->login("dat",'123456');
$ret = $client->get(array());
\Zend\Debug\Debug::dump($ret);
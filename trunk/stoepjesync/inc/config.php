<?php
class Db_stoepje{
	var $hostname = "212.121.112.90\SQLEXPRESS";            //host
	var $dbname = "WEB_Dynamics01";            //db name
	var $username = "funnyfox";            // username like 'sa'
	var $pw = "Fun2012#";                // password for the user
	
	/*
	var $hostname = "LENOVO-PC\SQLEXPRESS";            //host
	var $dbname = "WEB_Dynamics01";            //db name
	var $username = "tuandt";            // username like 'sa'
	var $pw = "123456";                // password for the user
	*/
	var $magentoDomain = 'http://37.34.57.27/stoepjeshop/index.php/api/v2_soap/?wsdl';
	var $magentoUsername = 'sync';
	var $magentoAPIKey = '98d87378ea3106a8f8e08bff9a9eea32';
	var $productCronjobTime = 36000;
	var $storeCronjobTime = 36000;
}
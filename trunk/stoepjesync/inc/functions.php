<?php
$config['db_host'] = 'localhost';
$config['db_user'] = 'stoepje';
$config['db_pass'] = 'shop234543';
$config['db_name'] = 'stoepjeshop';
$config['db_table'] = 'storepicker_stores';

$conn = null;
function db_init(){
	global $conn;
	global $config;
	$conn = mysql_connect($config['db_host'],$config['db_user'],$config['db_pass']);
	if (!$conn) {
		die('Could not connect: ' . mysql_error());
	}
	$db = mysql_select_db($config['db_name']);
	if(!$db){
		die('Could not connect: ' . mysql_error());
	}
}
function getAllStoreList(){
	global $config;
	$result=mysql_query("SELECT * FROM ".$config['db_table']." ORDER BY importid ASC ");
	if($result)
	{
		return $result;
	}else{
		die('Error');
		return false;
	}
}
function updateStore($importId, $data){
	global $config;
	$result=mysql_query("UPDATE ".$config['db_table']." SET ". arrayToString(',', $data, true) . "WHERE importid = '" . $importId."'");
	return $result;
}
function insertStore($importId, $data){
	global $config;
	$result=mysql_query("INSERT INTO ".$config['db_table']."(customerno, name, street, housenr, postal, city, country, phone, email, textualpos, longitude, latitude, banknr, pickup, send, sendcosts, ghostfrom, opensunday, openmonday, opentuesday, openwednesday, openthursday, openfriday, opensaturday, sendradius, sendsunday, sendmonday, sendtuesday, sendwednesday, sendthursday, sendfriday, sendsaturday, importid) VALUE(". arrayToString(',', $data) .")");
	/*
	$result1="INSERT INTO ".$config['db_table']."(customerno, name, street, housenr, postal, city, country, phone, email, textualpos, longitude, latitude, banknr, pickup, send, sendcosts, ghostfrom, opensunday, openmonday, opentuesday, openwednesday, openthursday, openfriday, opensaturday, sendradius, sendsunday, sendmonday, sendtuesday, sendwednesday, sendthursday, sendfriday, sendsaturday, importid) VALUE(". arrayToString(',', $data) .")";
	var_dump($result1);
	
	echo mysql_errno();
	echo mysql_error();
	*/
	return $result;
}
function deleteStore($importId){
	global $config;
	$result=mysql_query("DELETE FROM ".$config['db_table']." WHERE importid=".$importId);
	return $result;
}
function db_close(){
	global $conn;
	mysql_close($conn);
}
function arrayToString($glue, $pieces,$includeKey = false){
	$str = '';
	foreach ($pieces as $key => $val){
		if($includeKey){
			$str .= $key . ' =\''. $val . '\', ';
		}else{
			$str .= '\''. $val . '\', ';
		}
	}
	$str = trim($str);
	if(strlen($str) > 0){
		$str = substr($str, 0, strlen($str) - 1);
	}
	return $str;
}
?>
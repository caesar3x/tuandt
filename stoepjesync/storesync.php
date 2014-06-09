<?php
require_once 'inc/config.php';
require_once 'inc/functions.php';
require_once 'inc/Nav.class.php';
require_once 'inc/Sync.class.php';
ini_set('display_errors', '1');
date_default_timezone_set('EST');
$nav = new Nav();
$nav->init();
$navStoreList = $nav->getStoreList();

if($navStoreList){
	
	db_init();
	$db_stoepje = new Db_stoepje();
	$storeList = getAllStoreList();
	$storeImportidList = array();
	if($storeList){
		while ($row = mysql_fetch_assoc($storeList)) {
			$storeImportidList[] = $row['importid'];
		}
	}
	
	$navStoreImportidList = array();
	$i = 0;
	foreach ($navStoreList as $navStore){
		
		$lastModified = strtotime($navStore['Last DateTime Modified']);
		
		$navStoreImportidList[] = intval($navStore['ID']);
		
		if((time() - $lastModified) > $db_stoepje->storeCronjobTime){
			//continue;
		}
		$i++;
		if(in_array($navStore['ID'], $storeImportidList)){
			$geocodeURl = 'http://maps.googleapis.com/maps/api/geocode/json?address='. urlencode($navStore['Street'] . ', '. $navStore['Postal'].' '.$navStore['City']. ', The Netherlands').'&sensor=false';
			$lat = 0;
			$lon = 0;
			$ch = curl_init ($geocodeURl);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
			sleep(1);
			$geocodeData = curl_exec($ch);
			$geocodeData = json_decode($geocodeData);
			if($geocodeData->status == "OK"){
				$geoCode = $geocodeData->results[0];
				$lat = $geoCode->geometry->location->lat;
				$lon = $geoCode->geometry->location->lng;
				
			}
			$opensunday = '-';
			$openmonday = '-';
			$opentuesday = '-';
			$openwednesday = '-';
			$openthursday = '-';
			$openfriday = '-';
			$opensaturday = '-';
			$sendsunday = '-';					
			$sendmonday = '-';
			$sendtuesday = '-';
			$sendwednesday = '-';
			$sendthursday = '-';
			$sendfriday = '-';
			$sendsaturday = '';
			switch ($navStore['Market day']){
				case 1:
					$openmonday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendmonday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 2:
					$opentuesday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendtuesday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 3:
					$openwednesday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendwednesday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 4:
					$openthursday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendthursday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 5:
					$openfriday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendfriday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 6:
					$opensaturday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendsaturday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 7:
					$opensunday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendsunday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				default:
					break;
			}
			$storeData = array(
				'customerno' => $navStore['Customer No'],
				'name' => mysql_real_escape_string($navStore['Name']),
				'street' => mysql_real_escape_string($navStore['Street']),
				'housenr' => '',
				'postal' => $navStore['Postal'],
				'city' => mysql_real_escape_string($navStore['City']),
				'country' => $navStore['Country'],
				'phone' => $navStore['Phone'],
				'email' => $navStore['E-mail'],
				'textualpos' => '',
				'longitude' => $lon,
				'latitude' => $lat,
				'banknr' => $navStore['Banknr'],
				'pickup' => $navStore['Pickup'],
				'send' => $navStore['Send'],
				'sendcosts' => 0,
				'ghostfrom' => $navStore['Ghostfrom'],
				'opensunday' => $opensunday,
				'openmonday' => $openmonday,
				'opentuesday' => $opentuesday,
				'openwednesday' => $openwednesday,
				'openthursday' => $openthursday,
				'openfriday' => $openfriday,
				'opensaturday' => $opensaturday,
				'sendradius' => round($navStore['Delivery Area (km)'], 1),
				'sendsunday' => $sendsunday,
				'sendmonday' => $sendmonday,
				'sendtuesday' => $sendtuesday,
				'sendwednesday' => $sendwednesday,
				'sendthursday' => $sendthursday,
				'sendfriday' => $sendfriday,
				'sendsaturday' => $sendsaturday
			);
			updateStore($navStore['ID'], $storeData);
			echo $i. " Updated store ID: ". $navStore['ID'] . " Lon: " . $lon . " ,Lat: ". $lat . "<br/>";
		}else{
			$geocodeURl = 'http://maps.googleapis.com/maps/api/geocode/json?address='. urlencode($navStore['Street'] . ', '. $navStore['Postal'].' '.$navStore['City']. ', The Netherlands').'&sensor=false';
			$lat = 0;
			$lon = 0;
			$ch = curl_init ($geocodeURl);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
			sleep(1);
			$geocodeData = curl_exec($ch);
			$geocodeData = json_decode($geocodeData);
			if($geocodeData->status == "OK"){
				$geoCode = $geocodeData->results[0];
				$lat = $geoCode->geometry->location->lat;
				$lon = $geoCode->geometry->location->lng;
				
			}
			$opensunday = '-';
			$openmonday = '-';
			$opentuesday = '-';
			$openwednesday = '-';
			$openthursday = '-';
			$openfriday = '-';
			$opensaturday = '-';
			$sendsunday = '-';					
			$sendmonday = '-';
			$sendtuesday = '-';
			$sendwednesday = '-';
			$sendthursday = '-';
			$sendfriday = '-';
			$sendsaturday = '-';
			switch ($navStore['Market day']){
				case 1:
					$openmonday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendmonday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 2:
					$opentuesday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendtuesday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 3:
					$openwednesday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendwednesday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 4:
					$openthursday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendthursday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 5:
					$openfriday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendfriday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 6:
					$opensaturday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendsaturday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				case 7:
					$opensunday = substr($navStore['Starting Time Market'], 11 ,5).' - '. substr($navStore['Ending Time Market'], 11 ,5);
					$sendsunday = substr($navStore['Delivery Time From'], 11 ,5).' - '. substr($navStore['Delivery Time To'], 11 ,5);
					break;
				default:
					break;
			}
			$storeData = array(
				'customerno' => $navStore['Customer No'],
				'name' => mysql_real_escape_string($navStore['Name']),
				'street' => mysql_real_escape_string($navStore['Street']),
				'housenr' => '',
				'postal' => $navStore['Postal'],
				'city' => mysql_real_escape_string($navStore['City']),
				'country' => $navStore['Country'],
				'phone' => $navStore['Phone'],
				'email' => $navStore['E-mail'],
				'textualpos' => '',
				'longitude' => $lon,
				'latitude' => $lat,
				'banknr' => $navStore['Banknr'],
				'pickup' => $navStore['Pickup'],
				'send' => $navStore['Send'],
				'sendcosts' => 0,
				'ghostfrom' => $navStore['Ghostfrom'],
				'opensunday' => $opensunday,
				'openmonday' => $openmonday,
				'opentuesday' => $opentuesday,
				'openwednesday' => $openwednesday,
				'openthursday' => $openthursday,
				'openfriday' => $openfriday,
				'opensaturday' => $opensaturday,
				'sendradius' => round($navStore['Delivery Area (km)'], 1),
				'sendsunday' => $sendsunday,
				'sendmonday' => $sendmonday,
				'sendtuesday' => $sendtuesday,
				'sendwednesday' => $sendwednesday,
				'sendthursday' => $sendthursday,
				'sendfriday' => $sendfriday,
				'sendsaturday' => $sendsaturday,
				'importid'	=> intval($navStore['ID'])
			);
			insertStore($navStore['ID'], $storeData);
			echo $i . " Inserted store ID: ". $navStore['ID'] . " Lon: " . $lon . " ,Lat: ". $lat . "<br/>";
		}
	}

	if(count($navStoreImportidList) > 0){
		$j = 0;
		foreach ($storeImportidList as $storeImportid){
			if(!in_array($storeImportid,$navStoreImportidList)){
				deleteStore(intval($storeImportid));
				$j++;
				echo $j . " Deleted store ID: ".$storeImportid . "<br/>";
			}
		}
	}
	db_close();
}

?>
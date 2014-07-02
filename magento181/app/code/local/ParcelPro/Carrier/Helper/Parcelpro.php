<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Usa data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ParcelPro_Carrier_Helper_Parcelpro extends Mage_Core_Helper_Abstract
{
	private $_base_url;
	private $_version;
	private $_auth_path;
	private $_location_path;
	private $_addressbook_path;
	
	private $_single_quote_path;
	private $_shipment_path;
	private $_highvalue_path;
	private $_estimator_path;
	private $_carrierservices_path;
	private $_packagetypes_path;
	private $_zipcodevalidator_path;
	private $_addressvalidator_path;
	
	private $_sessionId = null;
	
	protected $_code = 'parcelpro_carrier';
	
	public function __construct()
	{
		$this->_base_url = 'https://apibeta.parcelpro.com/';
		
		if(Mage::getStoreConfig('carriers/parcelpro/sandbox_mode') == 1){
			$this->_base_url = 'https://apibeta.parcelpro.com/';
		}else{
			$this->_base_url = 'https://api.parcelpro.com/';
		}

		$this->_version = 'v1.1/';
		//$this->_version = 'v1/';
		$this->_auth_path = 'auth';
		$this->_location_path = 'location';
		$this->_addressbook_path = 'addressbook';
		$this->_quote_path = 'quote';
		$this->_shipment_path = 'shipment';
		$this->_highvalue_path = 'highvalue';
		$this->_estimator_path = 'estimator';
		$this->_carrierservices_path = 'carrierservices';
		$this->_packagetypes_path = 'packagetypes';
		$this->_zipcodevalidator_path = 'zipcodevalidator';
		$this->_addressvalidator_path = 'addressvalidator';
	
	}
	public function updateSpecialServices(){
		$this->_deleteConfig('special_services');
		$model = Mage::getModel('carrier/config');
		$upsSpecialServices = array(
				'NO_SIGNATURE_REQUIRED' => Mage::helper('usa')->__('Not Required'),
				'DIRECT'                => Mage::helper('usa')->__('Direct Signature'),
				'saturday_pickup'              => Mage::helper('usa')->__('Saturday pickup'),
				'saturday_delivery'              => Mage::helper('usa')->__('Saturday delivery'),
				'thermal_label'              => Mage::helper('usa')->__('Thermal label'),
		);
		foreach ($upsSpecialServices as $key => $val){
			$model->setCarrier('UPS')
			->setConfigType('special_services')
			->setConfigKey($key)
			->setConfigValue($val)
			->setApplyTo('UPS')
			->save();
			$model->unsetData();
		}
		$fedexSpecialServices = array(
				'NO_SIGNATURE_REQUIRED' => Mage::helper('usa')->__('Not Required'),
				'ADULT'                 => Mage::helper('usa')->__('Adult Signature'),
				'DIRECT'                => Mage::helper('usa')->__('Direct Signature'),
				'saturday_pickup'              => Mage::helper('usa')->__('Saturday pickup'),
				'saturday_delivery'              => Mage::helper('usa')->__('Saturday delivery'),
				'thermal_label'              => Mage::helper('usa')->__('Thermal label'),
				
		);
		foreach ($fedexSpecialServices as $key => $val){
			$model->setCarrier('FEDEX')
			->setConfigType('special_services')
			->setConfigKey($key)
			->setConfigValue($val)
			->setApplyTo('FEDEX')
			->save();
			$model->unsetData();
		}
	}
	
	public function updatePackageTypes(){
		$model = Mage::getModel('carrier/config');
		$collection = $model->getCollection()
		->addFieldToFilter('config_type',array('eq'=>'carrier'));
		foreach($collection as $row){
			$this->_deleteConfig('packaging', $row->getConfigValue());
			$carrierServices = $model->getCollection()
				->addFieldToFilter('config_type',array('eq'=>'method'))->addFieldToFilter('carrier',array('eq'=>$row->getConfigValue()));
			foreach ($carrierServices as $carrierService){
				$queryString = 'carrierservicecode='.$carrierService->getConfigKey().'&carriercode='.$row->getConfigValue();
				$packageTypes = $this->_sendRequest($this->_packagetypes_path, $queryString, array());
				if($packageTypes){
					foreach ($packageTypes as $packageType){
						$model->setCarrier($row->getConfigValue())
						->setConfigType('packaging')
						->setConfigKey($packageType->PackageTypeCode)
						->setConfigValue($packageType->PackageTypeDesc)
						->setApplyTo($carrierService->getConfigKey())
						->save();
						$model->unsetData();
					}
				}
			}
		}
	}
	
	public function updateCarrierServices(){
		$model = Mage::getModel('carrier/config');
		$collection = $model->getCollection()
		->addFieldToFilter('config_type',array('eq'=>'carrier'));
		foreach($collection as $row){
			$this->_deleteConfig('method', $row->getConfigValue());
			$queryString = 'carriercode='.$row->getConfigValue().'&domesticonly=False';
			$carrierServices = $this->_sendRequest($this->_carrierservices_path, $queryString, array());
			if($carrierServices){
				foreach ($carrierServices as $service){
					$model->setCarrier($row->getConfigValue())
						->setConfigType('method')
						->setConfigKey($service->ServiceCode)
						->setConfigValue($service->ServiceCodeDesc)
						->save();
					$model->unsetData();
				}
			}
			
			$queryString = 'carriercode='.$row->getConfigValue().'&domesticonly=True';
			$carrierServices = $this->_sendRequest($this->_carrierservices_path, $queryString, array());
			if($carrierServices){
				foreach ($carrierServices as $service){
					$model->setCarrier($row->getConfigValue())
						->setConfigType('method')
						->setConfigKey($service->ServiceCode)
						->setConfigValue($service->ServiceCodeDesc)
						->save();
					$model->unsetData();
				}
			}
		}
	}
	
	public function updateCarriers(){
		$model = Mage::getModel('carrier/config');
        $this->_deleteConfig('carrier');
		$model->setCarrier('')
            ->setConfigType('carrier')
            ->setConfigKey('UPS')
            ->setConfigValue('UPS')
            ->setApplyTo('1')
            ->save();
		$model->unsetData();
        $model->setCarrier('')
        ->setConfigType('carrier')
        ->setConfigKey('FEDEX')
        ->setConfigValue('FEDEX')
        ->setApplyTo('2')
        ->save();
		return true;
	}
	
	private function _deleteConfig($type, $carrier = null){
		$model = Mage::getModel('carrier/config');
		if($carrier){
			$collection = $model->getCollection()
			->addFieldToFilter('config_type',array('eq'=>$type))->addFieldToFilter('carrier',array('eq'=>$carrier));
		}else{
			$collection = $model->getCollection()
				->addFieldToFilter('config_type',array('eq'=>$type));
		}
		foreach($collection as $row){
			$row->delete();
		}
	}
	private function _sendRequest($requestPath, $queryString, $parrams, $isPost = false){
		if(!$this->_sessionId){
			$this->_getSessionId();
		}
	
		if($this->_sessionId){
			$url = $this->_base_url . $this->_version . $requestPath . '?' . $queryString .'&sessionID=' . $this->_sessionId;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			if($isPost){
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parrams));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen(json_encode($parrams)))
				);
			}else{
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				)
				);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				
			$data = curl_exec ($ch);
			curl_close ($ch);
			if($data){
				if($isPost){
					$result = json_decode($data);
				}else{
					$result = json_decode($data);
				}
				return $result;
			}else{
				return null;
			}
		}else{
			return null;
		}
	}
	
	private function _getSessionId(){
		$model = Mage::getModel('carrier/config');
		$collection = $model->getCollection()
			->addFieldToFilter('config_type',array('eq'=>'session_id'));
		if($collection->count()){
			$item = $collection->getFirstItem();
			if($this->_checkInvalideSession($item->getConfigValue())){
				$this->_sessionId = $item->getConfigValue();
				return $item->getConfigValue();
			}else{
				$item->delete();
			}
		}
		
		$username = Mage::helper('core')->decrypt(Mage::getStoreConfig('carriers/parcelpro/account'));
		$password = Mage::helper('core')->decrypt(Mage::getStoreConfig('carriers/parcelpro/password'));
		$apikey = Mage::helper('core')->decrypt(Mage::getStoreConfig('carriers/parcelpro/key'));
		
		$ch = curl_init();
		$auth_url = $this->_base_url . $this->_version . $this->_auth_path;
		$auth_url .= '?username='.$username.'&password='.$password.'&apikey='.$apikey;
		
		curl_setopt($ch, CURLOPT_URL, $auth_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			)
		);
		
		$authData = curl_exec ($ch);
		curl_close ($ch);
		if($authData){
			$result = json_decode($authData);
			$this->_sessionId = $result->SessionID;
			$this->_checkInvalideSession($result->SessionID);
			
			$model->setCarrier("")
			->setConfigType('session_id')
			->setConfigKey('session_id')
			->setConfigValue($result->SessionID)
			->save();
			$model->unsetData();
			
			return $result->SessionID;
		}else{
			return false;
		}
	}
	
	private function _checkInvalideSession($sessionId){
		if($sessionId == null || $sessionId == ""){
			return false;
		}
		$ch = curl_init();
		$location_url = $this->_base_url . $this->_version . $this->_location_path;
		$location_url .= '?sessionID=' . $sessionId;
		
		curl_setopt($ch, CURLOPT_URL, $location_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$locationData = curl_exec ($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);
		if($http_status == '401'){
			return false;
		}else{
			return true;
		}
	}
}

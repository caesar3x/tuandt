<?php
class ParcelPro_Carrier_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {
	
	private $_base_url;
	private $_version;
	private $_auth_path;
	private $_location_path;
	private $_addressbook_path;
	
	private $_single_quote_path;
	private $_quote_path;
	private $_shipment_path;
	private $_highvalue_path;
	private $_estimator_path;
	private $_carrierservices_path;
	private $_packagetypes_path;
	private $_zipcodevalidator_path;
	private $_addressvalidator_path;
	
	private $_sessionId = null;
	
	protected $_code = 'parcelpro';
	
	/**
	 * Rate result data
	 *
	 * @var Mage_Shipping_Model_Rate_Result|null
	 */
	protected $_result = null;
	
	public function __construct()
	{
		if($this->getConfigData('sandbox_mode') == 1){
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
	
	public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
		$result = Mage::getModel('shipping/rate_result');
		if($this->getConfigData('allowed_carriers')){
			$_rawRequest = $this->_collectRequestData($request);
			//Mage::log(json_encode($_rawRequest));
			$estimatorData = $this->_sendRequest($this->_estimator_path, $_rawRequest, true);
			
			$rateList = $this->_parseShippingRates($estimatorData, $request);

			if($rateList){
				foreach ($rateList as $rate){
					$result->append($rate);
				}
			}
			$this->_result = $result;
		}
		return $result;
		
	}
	public function getAllowedMethods() {
		$arr = array();
		$allMethods = $this->getCode('method');
		
		$allMethodOptions = array(); 
		if($allMethods){
			foreach ($allMethods as $key =>$val){
				$allMethodOptions[$key] = $val;
			}
		}
		$upsAllowed = explode(',', $this->getConfigData('allowed_ups_methods'));
		foreach ($upsAllowed as $k) {
			if(isset($allMethodOptions[$k])){
				$arr[$k] = $allMethodOptions[$k];
			}
		}
		
		$fedexAllowed = explode(',', $this->getConfigData('allowed_fedex_methods'));
		foreach ($fedexAllowed as $k) {
		if(isset($allMethodOptions[$k])){
				$arr[$k] = $allMethodOptions[$k];
			}
		}
		return $arr;
	}
	
	/**
	 * Check if carrier has shipping tracking option available
	 * All Mage_Usa carriers have shipping tracking option available
	 *
	 * @return boolean
	 */
	public function isTrackingAvailable()
	{
		return true;
	}
	
	public function isShippingLabelsAvailable()
	{
		return true;
	}
	
	/**
	 * Return items for further shipment rate evaluation. We need to pass children of a bundle instead passing the
	 * bundle itself, otherwise we may not get a rate at all (e.g. when total weight of a bundle exceeds max weight
	 * despite each item by itself is not)
	 *
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return array
	 */
	public function getAllItems(Mage_Shipping_Model_Rate_Request $request)
	{
		$items = array();
		if ($request->getAllItems()) {
			foreach ($request->getAllItems() as $item) {
				/* @var $item Mage_Sales_Model_Quote_Item */
				if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
					// Don't process children here - we will process (or already have processed) them below
					continue;
				}
	
				if ($item->getHasChildren() && $item->isShipSeparately()) {
					foreach ($item->getChildren() as $child) {
						if (!$child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
							$items[] = $child;
						}
					}
				} else {
					// Ship together - count compound item as one solid
					$items[] = $item;
				}
			}
		}
		return $items;
	}
	
	/**
	 * Processing additional validation to check if carrier applicable.
	 *
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return Mage_Shipping_Model_Carrier_Abstract|Mage_Shipping_Model_Rate_Result_Error|boolean
	 */
	/*
	public function proccessAdditionalValidation(Mage_Shipping_Model_Rate_Request $request)
	{
		//Skip by item validation if there is no items in request
		if(!count($this->getAllItems($request))) {
			return $this;
		}
	
		$maxAllowedWeight   = (float) $this->getConfigData('max_package_weight');
		$errorMsg           = '';
		$configErrorMsg     = $this->getConfigData('specificerrmsg');
		$defaultErrorMsg    = Mage::helper('shipping')->__('The shipping module is not available.');
		$showMethod         = $this->getConfigData('showmethod');
	
		foreach ($this->getAllItems($request) as $item) {
			if ($item->getProduct() && $item->getProduct()->getId()) {
				$weight         = $item->getProduct()->getWeight();
				$stockItem      = $item->getProduct()->getStockItem();
				$doValidation   = true;
	
				if ($stockItem->getIsQtyDecimal() && $stockItem->getIsDecimalDivided()) {
					if ($stockItem->getEnableQtyIncrements() && $stockItem->getQtyIncrements()) {
						$weight = $weight * $stockItem->getQtyIncrements();
					} else {
						$doValidation = false;
					}
				} elseif ($stockItem->getIsQtyDecimal() && !$stockItem->getIsDecimalDivided()) {
					$weight = $weight * $item->getQty();
				}
	
				if ($doValidation && $weight > $maxAllowedWeight) {
					$errorMsg = ($configErrorMsg) ? $configErrorMsg : $defaultErrorMsg;
					break;
				}
			}
		}
	
		if (!$errorMsg && !$request->getDestPostcode() && $this->isZipCodeRequired($request->getDestCountryId())) {
			$errorMsg = Mage::helper('shipping')->__('This shipping method is not available, please specify ZIP-code');
		}
	
		if ($errorMsg && $showMethod) {
			$error = Mage::getModel('shipping/rate_result_error');
			$error->setCarrier($this->_code);
			$error->setCarrierTitle($this->getConfigData('title'));
			$error->setErrorMessage($errorMsg);
			return $error;
		} elseif ($errorMsg) {
			return false;
		}
		return $this;
	}
	*/
	/**
	 * Returns cache key for some request to carrier quotes service
	 *
	 * @param string|array $requestParams
	 * @return string
	 */
	protected function _getQuotesCacheKey($requestParams)
	{
		if (is_array($requestParams)) {
			$requestParams = implode(',', array_merge(
					array($this->getCarrierCode()),
					array_keys($requestParams),
					$requestParams)
			);
		}
		return crc32($requestParams);
	}
	
	/**
	 * Checks whether some request to rates have already been done, so we have cache for it
	 * Used to reduce number of same requests done to carrier service during one session
	 *
	 * Returns cached response or null
	 *
	 * @param string|array $requestParams
	 * @return null|string
	 */
	protected function _getCachedQuotes($requestParams)
	{
		$key = $this->_getQuotesCacheKey($requestParams);
		return isset(self::$_quotesCache[$key]) ? self::$_quotesCache[$key] : null;
	}
	
	/**
	 * Sets received carrier quotes to cache
	 *
	 * @param string|array $requestParams
	 * @param string $response
	 * @return Mage_Usa_Model_Shipping_Carrier_Abstract
	 */
	protected function _setCachedQuotes($requestParams, $response)
	{
		$key = $this->_getQuotesCacheKey($requestParams);
		self::$_quotesCache[$key] = $response;
		return $this;
	}
	
	/**
	 * Prepare service name. Strip tags and entities from name
	 *
	 * @param string|object $name  service name or object with implemented __toString() method
	 * @return string              prepared service name
	 */
	protected function _prepareServiceName($name)
	{
		$name = html_entity_decode((string)$name);
		$name = strip_tags(preg_replace('#&\w+;#', '', $name));
		return trim($name);
	}
	
	
	public function getTrackingInfo($tracking)
	{
		$info = array();
	
		$result = $this->getTracking($tracking);
	
		if($result instanceof Mage_Shipping_Model_Tracking_Result){
			if ($trackings = $result->getAllTrackings()) {
				return $trackings[0];
			}
		}
		elseif (is_string($result) && !empty($result)) {
			return $result;
		}
	
		return false;
	}
	
	/**
	 * Get tracking
	 *
	 * @param mixed $trackings
	 * @return mixed
	 */
	public function getTracking($trackings)
	{
		$return = array();
	
		if (!is_array($trackings)) {
			$trackings = array($trackings);
		}
		
		$this->_getCgiTracking($trackings);
		
		return $this->_result;
	}
	
	/**
	 * Prepare shipment request.
	 * Validate and correct request information
	 *
	 * @param Varien_Object $request
	 *
	 */
	protected function _prepareShipmentRequest(Varien_Object $request)
	{
		$phonePattern = '/[\s\_\-\(\)]+/';
		$phoneNumber = $request->getShipperContactPhoneNumber();
		$phoneNumber = preg_replace($phonePattern, '', $phoneNumber);
		$request->setShipperContactPhoneNumber($phoneNumber);
		$phoneNumber = $request->getRecipientContactPhoneNumber();
		$phoneNumber = preg_replace($phonePattern, '', $phoneNumber);
		$request->setRecipientContactPhoneNumber($phoneNumber);
	}
	
	/**
	 * Do request to shipment
	 *
	 * @param Mage_Shipping_Model_Shipment_Request $request
	 * @return array
	 */
	public function requestToShipment(Mage_Shipping_Model_Shipment_Request $request)
	{
		$packages = $request->getPackages();
		if (!is_array($packages) || !$packages) {
			Mage::throwException(Mage::helper('usa')->__('No packages for request'));
		}
		if ($request->getStoreId() != null) {
			$this->setStore($request->getStoreId());
		}
		$data = array();
		foreach ($packages as $packageId => $package) {
			$request->setPackageId($packageId);
			$request->setPackagingType($package['params']['container']);
			$request->setPackageWeight($package['params']['weight']);
			$request->setPackageParams(new Varien_Object($package['params']));
			$request->setPackageItems($package['items']);
			$result = $this->_doShipmentRequest($request);
	
			if ($result->hasErrors()) {
				$this->rollBack($data);
				break;
			} else {
				$data[] = array(
						'tracking_number' => $result->getTrackingNumber(),
						'label_content'   => $result->getShippingLabelContent()
				);
			}
			if (!isset($isFirstRequest)) {
				$request->setMasterTrackingId($result->getTrackingNumber());
				$isFirstRequest = false;
			}
		}
	
		$response = new Varien_Object(array(
				'info'   => $data
		));
		if ($result->getErrors()) {
			$response->setErrors($result->getErrors());
		}
		return $response;
	}
	
	/**
	 * Do request to RMA shipment
	 *
	 * @param $request
	 * @return array
	 */
	public function returnOfShipment($request)
	{
		$request->setIsReturn(true);
		$packages = $request->getPackages();
		if (!is_array($packages) || !$packages) {
			Mage::throwException(Mage::helper('usa')->__('No packages for request'));
		}
		if ($request->getStoreId() != null) {
			$this->setStore($request->getStoreId());
		}
		$data = array();
		foreach ($packages as $packageId => $package) {
			$request->setPackageId($packageId);
			$request->setPackagingType($package['params']['container']);
			$request->setPackageWeight($package['params']['weight']);
			$request->setPackageParams(new Varien_Object($package['params']));
			$request->setPackageItems($package['items']);
			$result = $this->_doShipmentRequest($request);
	
			if ($result->hasErrors()) {
				$this->rollBack($data);
				break;
			} else {
				$data[] = array(
						'tracking_number' => $result->getTrackingNumber(),
						'label_content'   => $result->getShippingLabelContent()
				);
			}
			if (!isset($isFirstRequest)) {
				$request->setMasterTrackingId($result->getTrackingNumber());
				$isFirstRequest = false;
			}
		}
	
		$response = new Varien_Object(array(
				'info'   => $data
		));
		if ($result->getErrors()) {
			$response->setErrors($result->getErrors());
		}
		return $response;
	}
	
	/**
	 * Return container types of carrier
	 *
	 * @param Varien_Object|null $params
	 * @return array
	 */
	public function getContainerTypes(Varien_Object $params = null)
	{
		$model = Mage::getModel('carrier/config');
		if($params){
			$collection = $model->getCollection()
			->addFieldToFilter('config_type',array('eq'=>'packaging'))->addFieldToFilter('apply_to',array('eq'=>$params->getMethod()));
			if($collection){
				$containerTypes = array();
				foreach ($collection as $row){
					$containerTypes[$row->getConfigKey()] = $row->getConfigValue();
				}
				return $containerTypes;
			}else{
				return array();
			}
		}else{
			return $this->_getAllowedContainers($params);
		}
		return array();
	}
	
	public function rollBack($data)
	{
		return true;
	}
	
	protected function _doShipmentRequest(Varien_Object $request)
	{
		$result = new Varien_Object();
		$_rawShipmentRequest = $this->_collectShipmentRequestData($request);

		$quoteResult = $this->_sendRequest($this->_quote_path, $_rawShipmentRequest, true);
		if($quoteResult && $quoteResult->Estimator){
			$quoteData = $quoteResult->Estimator[0];
			$quoteID = $quoteData->QuoteID;
			$shipmentResult = $this->_sendRequestShipment($this->_shipment_path. '/' . $quoteID);
			
			if($shipmentResult){
				$result->setShippingLabelContent(base64_decode($shipmentResult->LabelImage));
				//$result->setTrackingNumber($shipmentResult->TrackingNumber);
				$result->setTrackingNumber($shipmentResult->InternalTrackingNumber);
				
			}else{
				$result->setErrors(Mage::helper('sales')->__('An error occurred while creating shipping label..'));
			}
		}else{
			if($quoteResult && isset($quoteResult->ErrorMessage)){
				$result->setErrors($quoteResult->ErrorMessage);
			}else{
				$result->setErrors(Mage::helper('sales')->__('An error occurred while creating shipping label. Separate your shipment or change the package type.'));
			}
		}
		return $result;
	}
	
	private function _collectShipmentRequestData(Varien_Object $request){
		$this->_prepareShipmentRequest($request);
		$contacts = $this->_sendRequest($this->_location_path, array(), false);
		if(!$contacts){
			return null;
		}
		$contactId = $contacts[0]->ContactId;
		
		if ($request->getShipperAddressCountryCode() != $request->getRecipientAddressCountryCode()) {
			$IsResidential = false;
		}else{
			$IsResidential = true;
		}
		$packageType = $request->getPackagingType();
		
		$CarrierCode = "2";
		$model = Mage::getModel('carrier/config');
		$collection = $model->getCollection()
			->addFieldToFilter('config_type',array('eq'=>'method'))->addFieldToFilter('config_key',array('eq'=>$request->getShippingMethod()));
		if($collection){
			$Carrier = $collection->getFirstItem()->getCarrier();
			$CarrierData = $model->getCollection()
				->addFieldToFilter('config_type',array('eq'=>'carrier'))->addFieldToFilter('config_key',array('eq'=>$Carrier));
			if($CarrierData){
				$CarrierCode = $CarrierData->getFirstItem()->getApplyTo();
			}
		}
		$CarrierCode = intval($CarrierCode);
		
		$productIds = array();
		$packageParams = $request->getPackageParams();
		$packageItems = $request->getPackageItems();
		$insured = 0;
		
		foreach ($packageItems as $itemShipment) {
			$item = new Varien_Object();
			$item->setData($itemShipment);
			
			$product = Mage::getModel('catalog/product')->load( $item->getProductId() );
			if ($product->isVirtual() || $product->getParentItem()) {
				continue;
			}
			
			if ($item->getHasChildren() && $item->isShipSeparately()) {
				foreach ($item->getChildren() as $child) {
					if (!$child->getProduct()->isVirtual()) {
						$product = Mage::getModel('catalog/product')->load( $item->getProductId() );
							if($product->getInsured()){
								$insured += $product->getInsured() * $item->getQty();
							}else if($product->getCost()){
								$insured += $product->getCost() * $item->getQty();
							}else{
								$insured += $product->getPrice() * $item->getQty();
							}
					}
				}
			} else{
				if($product->getInsured()){
					$insured += $product->getInsured() * $item->getQty();
				}else if($product->getCost()){
					$insured += $product->getCost() * $item->getQty();
				}else{
					$insured += $product->getPrice() * $item->getQty();
				}
			}
		}
		
		// get countries of manufacture
		$productCollection = Mage::getResourceModel('catalog/product_collection')
		->addStoreFilter($request->getStoreId())
		->addFieldToFilter('entity_id', array('in' => $productIds))
		->addAttributeToSelect('country_of_manufacture');
		foreach ($productCollection as $product) {
			$countriesOfManufacture[] = $product->getCountryOfManufacture();
		}
		
		
		if($this->getConfigData('saturday_pickup') == '0'){
			$isSaturdayPickUp = false;
		}else{
			$isSaturdayPickUp = true;
		}
		
		if($this->getConfigData('saturday_delivery') == '0'){
			$isSaturdayDelivery = false;
		}else{
			$isSaturdayDelivery = true;
		}
		
		if($this->getConfigData('delivery_confirmation') == '0'){
			$isDeliveryConfirmation = false;
		}else{
			$isDeliveryConfirmation = true;
		}
		
		if($this->getConfigData('regular_pickup') == '0'){
			$isRegularPickUp = false;
		}else{
			$isRegularPickUp = true;
		}
		if($this->getConfigData('drop_off') == '0'){
			$isDropoff = false;
		}else{
			$isDropoff = true;
		}
		if($this->getConfigData('thermal_label') == '0'){
			$isThermal = false;
		}else{
			$isThermal = true;
		}
		
		if($request->getRecipientContactCompanyName()){
			$IsResidential = false;
		}else{
			$IsResidential = true;
		}
		$isDirectSignature = false;
		$deliveryConfirmation = $packageParams->getDeliveryConfirmation();
		
		if($deliveryConfirmation == 'ADULT'){
			$isDeliveryConfirmation = true;
		}else if($deliveryConfirmation == 'DIRECT'){
			$isDirectSignature = true;
		}else if($deliveryConfirmation == 'thermal_label'){
			$isThermal = true;
		}
		
		if($IsResidential && $CarrierCode == 2){
			$isDirectSignature = true;
		}
		
		$requestShipmentData = array(
				"ShipmentId" => "NOID",
				"QuoteId" => "",
				"CustomerId" => "NOID",
				"UserId" => "NOID",
				"ShipToResidential" => $IsResidential,
				"ServiceCode" => $request->getShippingMethod(),
				"CarrierCode" => $CarrierCode,
				"ShipTo" => array(
						"ContactId" => "NOID",
						"CustomerId" => "",
						"UserId" => "",
						"ContactType" => 11,
						"CompanyName" => $request->getRecipientContactCompanyName()."",
						"FirstName" => $request->getRecipientContactPersonFirstName(),
						"LastName" => $request->getRecipientContactPersonLastName(),
						"StreetAddress" => $request->getRecipientAddressStreet1(),
						"ApartmentSuite" => "",
						"ProvinceRegion" => "",
						"City" => $request->getRecipientAddressCity(),
						"State" => $request->getRecipientAddressStateOrProvinceCode(),
						"Country" => $request->getRecipientAddressCountryCode(),
						"Zip" => $request->getRecipientAddressPostalCode(),
						"TelephoneNo" => $request->getRecipientContactPhoneNumber(),
						"FaxNo" => "",
						"Email" => $request->getOrderShipment()->getOrder()->getCustomerEmail(),
						"NickName" => "",
						"IsExpress" => false,
						"IsResidential" => $IsResidential,
						"IsUserDefault" => false,
						"UPSPickUpType" => 0,
						"TotalContacts" => "0"
				),
				"UpdateAddressBook" => false,
				"NotifyRecipient" => false,
				"ShipFrom" => array(
						"ContactId" => $contactId,
						//"ContactId" => "NOID",
						"CustomerId" => "",
						"UserId" => "",
						"ContactType" => 3,
						"CompanyName" => "",
						"FirstName" => "",
						"LastName" => "",
						"StreetAddress" => "",
						"ApartmentSuite" => "",
						"ProvinceRegion" => "",
						"City" => "",
						"State" => "",
						"Country" => "",
						"Zip" => "",
						"TelephoneNo" => "",
						"FaxNo" => "",
						"Email" => "",
						"NickName" => "",
						"IsExpress" => false,
						"IsResidential" => false,
						"IsUserDefault" => false,
						"UPSPickUpType" => 0,
						"TotalContacts" => "0"
				),
				"ShipDate" => date("Y-m-d"), //"2014-04-13",
				"PackageCode" => $request->getPackagingType(),
				"Height" => $packageParams->getHeight()?$packageParams->getHeight():0,
				"Width" => $packageParams->getWidth()?$packageParams->getWidth():0,
				"Length" => $packageParams->getLength()?$packageParams->getLength():0,
				"Weight" => $request->getPackageWeight()?floatval($request->getPackageWeight()):0,
				"InsuredValue" => $insured,
				"IsSaturdayPickUp" => $isSaturdayPickUp,
				"IsSaturdayDelivery" => $isSaturdayDelivery,
				"IsDeliveryConfirmation" => $isDeliveryConfirmation,
				//"IsCod" => false,
				//"CodAmount" => 0.0,
				"IsSecuredCod" => false,
				"IsRegularPickUp" => $isRegularPickUp,
				"IsDropoff" => $isDropoff,
				"IsPickUpRequested" => false,
				"IsSmartPickUp" => false,
				"PickUpContactName" => "",
				"PickUpTelephone" => "",
				"PickUpAtHour" => "",
				"PickUpAtMinute" => "",
				"PickUpByHour" => "",
				"PickUpByMinute" => "",
				"PickUpDate" => "",
				"DispatchConfirmationNumber" => "",
				"DispatchLocation" => "",
				"NotifySender" => false,
				"ReferenceNumber" => "",
				"TrackingNumber" => "",
				"CustomerReferenceNumber" => "",
				"IsDirectSignature" => $isDirectSignature,
				"IsThermal" => $isThermal,
				"IsMaxCoverageExceeded" => false,
				"Estimator" => array(
		
				),
				"LabelImage" => null,
				"IsBillToThirdParty" => false,
				"BillToThirdPartyPostalCode" => "",
				"BillToAccount" => "",
				"IsShipFromRestrictedZip" => false,
				"IsShipToRestrictedZip" => false,
				"IsShipToHasRestrictedWords" => false,
				"IsShipFromHasRestrictedWords" => false,
				"IsHighValueShipment" => false,
				"IsHighValueReport" => false,
				"ReceivedBy" => "",
				"ReceivedTime" => "",
				"TotalShipments" => "0"
		);
		//echo json_encode($requestShipmentData);
		return $requestShipmentData;
	}
	
	/**
	 * Get cgi tracking
	 *
	 * @param mixed $trackings
	 * @return mixed
	 */
	protected function _getCgiTracking($trackings)
	{
		//ups no longer support tracking for data streaming version
		//so we can only reply the popup window to ups.
		$result = Mage::getModel('shipping/tracking_result');
		$defaults = $this->getDefaults();
		foreach($trackings as $tracking){
			$status = Mage::getModel('shipping/tracking_result_status');
			$status->setCarrier($this->_code);
			$status->setCarrierTitle($this->getConfigData('title'));
			$status->setTracking($tracking);
			$status->setPopup(1);
			$status->setUrl("http://notify.parcelpro.com/track/$tracking");
            $result->append($status);
		}
	
		$this->_result = $result;
			return $result;
	}
	
	/**
	 * Get result of request
	 *
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->_result;
	}
	
	/**
	 * Return delivery confirmation types of carrier
	 *
	 * @param Varien_Object|null $params
	 * @return array
	 */
	public function getDeliveryConfirmationTypes(Varien_Object $params = null)
	{
		$shipment = Mage::registry('current_shipment');
		if($shipment){
			$order = $shipment->getOrder();
			$model = Mage::getModel('carrier/config');
			$collection = $model->getCollection()
				->addFieldToFilter('config_type',array('eq'=>'method'))->addFieldToFilter('config_key',array('eq'=>str_replace("parcelpro_", "", $order->getShippingMethod())));
			if($collection){
				if(count($collection) == 1){ 
					$carrier = $collection;
				}else{
					$carrier = $collection[0];
				}

				if($carrier->carrier == 'UPS'){
					return array(
							'NO_SIGNATURE_REQUIRED' => Mage::helper('usa')->__('Not Required'),
							'ADULT'                 => Mage::helper('usa')->__('Adult Signature'),
					);
				}else{
					return array(
							'NO_SIGNATURE_REQUIRED' => Mage::helper('usa')->__('Not Required'),							
							'DIRECT'                => Mage::helper('usa')->__('Direct Signature'),
					);
				}
			}else{
				return array(
						'NO_SIGNATURE_REQUIRED' => Mage::helper('usa')->__('Not Required'),
						'ADULT'                 => Mage::helper('usa')->__('Adult Signature'),
						'DIRECT'                => Mage::helper('usa')->__('Direct Signature'),
				);
			}
			
		}else{
			return array(
                'NO_SIGNATURE_REQUIRED' => Mage::helper('usa')->__('Not Required'),
				'ADULT'                 => Mage::helper('usa')->__('Adult Signature'),
				'DIRECT'                => Mage::helper('usa')->__('Direct Signature'),
            );
		}
	}
	
	/**
	 * Get configuration data of carrier
	 *
	 * @param string $type
	 * @param string $code
	 * @return array|bool
	 */
	public function getCode($type, $carrier='')
	{
		/*
		$codes = array(
				'method' => array(
						'01' => Mage::helper('usa')->__('Next Day Air'),
						'02' => Mage::helper('usa')->__('2nd Day Air'),
						'01-INT' => Mage::helper('usa')->__('International Priority'),
						'01-DOM' => Mage::helper('usa')->__('Priority Overnight'),
						'03-DOM' => Mage::helper('usa')->__('2 Day'),
						'05' => Mage::helper('usa')->__('Standard Overnight'),
				),
				'dropoff' => array(
						'REGULAR_PICKUP'          => Mage::helper('usa')->__('Regular Pickup'),
						'REQUEST_COURIER'         => Mage::helper('usa')->__('Request Courier'),
						'DROP_BOX'                => Mage::helper('usa')->__('Drop Box'),
						'BUSINESS_SERVICE_CENTER' => Mage::helper('usa')->__('Business Service Center'),
						'STATION'                 => Mage::helper('usa')->__('Station')
				),
				'packaging' => array(
						'25' => Mage::helper('usa')->__('10KG BOX'),
						'24'      => Mage::helper('usa')->__('25KG BOX'),
						'21'      => Mage::helper('usa')->__('EXPRESS BOX'),
						'01'     => Mage::helper('usa')->__('LETTER'),
						'04' => Mage::helper('usa')->__('PAK'),
						'03' => Mage::helper('usa')->__('TUBE'),
						'LARGE BOX' => Mage::helper('usa')->__('LARGE FEDEX BOX'),
						'MEDIUM BOX' => Mage::helper('usa')->__('MEDIUM FEDEX BOX'),
						'SMALL BOX' => Mage::helper('usa')->__('SMALL FEDEX BOX'),
						'02' => Mage::helper('usa')->__('Your Packaging...')
				),
				'containers_filter' => array(
						array(
								'containers' => array('FEDEX_ENVELOPE', 'FEDEX_PAK'),
								'filters'    => array(
										'within_us' => array(
												'method' => array(
														'FEDEX_EXPRESS_SAVER',
														'FEDEX_2_DAY',
														'FEDEX_2_DAY_AM',
														'STANDARD_OVERNIGHT',
														'PRIORITY_OVERNIGHT',
														'FIRST_OVERNIGHT',
												)
										),
										'from_us' => array(
												'method' => array(
														'INTERNATIONAL_FIRST',
														'INTERNATIONAL_ECONOMY',
														'INTERNATIONAL_PRIORITY',
												)
										)
								)
						),
						array(
								'containers' => array('FEDEX_BOX', 'FEDEX_TUBE'),
								'filters'    => array(
										'within_us' => array(
												'method' => array(
														'FEDEX_2_DAY',
														'FEDEX_2_DAY_AM',
														'STANDARD_OVERNIGHT',
														'PRIORITY_OVERNIGHT',
														'FIRST_OVERNIGHT',
														'FEDEX_FREIGHT',
														'FEDEX_1_DAY_FREIGHT',
														'FEDEX_2_DAY_FREIGHT',
														'FEDEX_3_DAY_FREIGHT',
														'FEDEX_NATIONAL_FREIGHT',
												)
										),
										'from_us' => array(
												'method' => array(
														'INTERNATIONAL_FIRST',
														'INTERNATIONAL_ECONOMY',
														'INTERNATIONAL_PRIORITY',
												)
										)
								)
						),
						array(
								'containers' => array('FEDEX_10KG_BOX', 'FEDEX_25KG_BOX'),
								'filters'    => array(
										'within_us' => array(),
										'from_us' => array('method' => array('INTERNATIONAL_PRIORITY'))
								)
						),
						array(
								'containers' => array('YOUR_PACKAGING'),
								'filters'    => array(
										'within_us' => array(
												'method' =>array(
														'FEDEX_GROUND',
														'GROUND_HOME_DELIVERY',
														'SMART_POST',
														'FEDEX_EXPRESS_SAVER',
														'FEDEX_2_DAY',
														'FEDEX_2_DAY_AM',
														'STANDARD_OVERNIGHT',
														'PRIORITY_OVERNIGHT',
														'FIRST_OVERNIGHT',
														'FEDEX_FREIGHT',
														'FEDEX_1_DAY_FREIGHT',
														'FEDEX_2_DAY_FREIGHT',
														'FEDEX_3_DAY_FREIGHT',
														'FEDEX_NATIONAL_FREIGHT',
												)
										),
										'from_us' => array(
												'method' =>array(
														'INTERNATIONAL_FIRST',
														'INTERNATIONAL_ECONOMY',
														'INTERNATIONAL_PRIORITY',
														'INTERNATIONAL_GROUND',
														'FEDEX_FREIGHT',
														'FEDEX_1_DAY_FREIGHT',
														'FEDEX_2_DAY_FREIGHT',
														'FEDEX_3_DAY_FREIGHT',
														'FEDEX_NATIONAL_FREIGHT',
														'INTERNATIONAL_ECONOMY_FREIGHT',
														'INTERNATIONAL_PRIORITY_FREIGHT',
												)
										)
								)
						)
				),
	
				'delivery_confirmation_types' => array(
						'NO_SIGNATURE_REQUIRED' => Mage::helper('usa')->__('Not Required'),
						'ADULT'                 => Mage::helper('usa')->__('Adult'),
						'DIRECT'                => Mage::helper('usa')->__('Direct'),
						'INDIRECT'              => Mage::helper('usa')->__('Indirect'),
				),
	
				'unit_of_measure'=>array(
						'LB'   =>  Mage::helper('usa')->__('Pounds'),
						'KG'   =>  Mage::helper('usa')->__('Kilograms'),
				),
		);
		*/
		$model = Mage::getModel('carrier/config');
		if($carrier){
			$collection = $model->getCollection()
				->addFieldToFilter('config_type',array('eq'=>$type))->addFieldToFilter('carrier',array('eq'=>$carrier));
		}else{
			$collection = $model->getCollection()
				->addFieldToFilter('config_type',array('eq'=>$type));
		}
		if($collection){
			$data = array();
			foreach ($collection  as $row){
				$data[$row->getConfigKey()] = $row->getConfigValue();
			}
			return $data;
		}else{
			return null;
		}
		
	}
	private function _parseShippingRates($estimatorData, $request){
		if(!isset( $estimatorData->Estimator)){
			return null;
		}
		
		$allowed_carriers = explode(',', strtoupper($this->getConfigData('allowed_carriers')));
		$estimatorList = $estimatorData->Estimator;
		if($estimatorList){
			$allowedMethods = $this->getAllowedMethods();
			if($allowedMethods){
				$alloewdMethodIds = array();
				
				foreach ($allowedMethods as $key => $val){
					$alloewdMethodIds[] = $key;
				}
				
				$rateList = array();
				foreach ($estimatorList as $estimator){
					if(in_array($estimator->ServiceCode, $alloewdMethodIds) && in_array(strtoupper($estimator->CarrierCode), $allowed_carriers) ){
						$rate = Mage::getModel('shipping/rate_result_method');
						/* @var $rate Mage_Shipping_Model_Rate_Result_Method */
							
						$rate->setCarrier($this->_code);
						$rate->setCarrierTitle($this->getConfigData('title'));
							
						$rate->setMethod($estimator->ServiceCode);
						$rate->setMethodTitle($estimator->ServiceCodeDescription);
							
						//$rate->setPrice($estimator->TotalCharges);
						
						$rate->setPrice($estimator->ServiceCode == $this->getConfigData($this->_freeMethod)
				            && $this->getConfigFlag('free_shipping_enable')
				            && $this->getConfigData('free_shipping_subtotal') <= $request->getBaseSubtotalInclTax()
				            ? '0.00': $estimator->TotalCharges);
						
						$rate->setCost($estimator->AccessorialsCost);
							
						$rateList[] = $rate;
					}
				}
				return $rateList;
			}else{
				return null;
			}
		}else{
			return null;
		}
	}
	
	private function _collectRequestData($request){
		//Mage::log($request->debug());		
		$_rawRequest = array();
		
		$insured = 0;
		if ($request->getAllItems()) {
			foreach ($request->getAllItems() as $item) {
				$product = Mage::getModel('catalog/product')->load( $item->getProductId() );
				if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
					continue;
				}
		
				if ($item->getHasChildren() && $item->isShipSeparately()) {
					foreach ($item->getChildren() as $child) {
						if (!$child->getProduct()->isVirtual()) {
							$product = Mage::getModel('catalog/product')->load( $item->getProductId() );
							if($product->getInsured()){
								$insured += $product->getInsured() * $item->getQty();
							}else if($product->getCost()){
								$insured += $product->getCost() * $item->getQty();
							}else{
								$insured += $product->getPrice() * $item->getQty();
							}
						}
					}
				} else{
					if($product->getInsured()){
						$insured += $product->getInsured() * $item->getQty();
					}else if($product->getCost()){
						$insured += $product->getCost() * $item->getQty();
					}else{
						$insured += $product->getPrice() * $item->getQty();
					}
				}
			}
		}
		
		$contacts = $this->_sendRequest($this->_location_path, array(), false);
		if(!$contacts){
			return null;
		}
		$contactId = $contacts[0]->ContactId;

		if($this->getConfigData('saturday_pickup') == '0'){
			$isSaturdayPickUp = false;
		}else{
			$isSaturdayPickUp = true;
		}
		
		if($this->getConfigData('saturday_delivery') == '0'){
			$isSaturdayDelivery = false;
		}else{
			$isSaturdayDelivery = true;
		}
		
		if($this->getConfigData('delivery_confirmation') == '0'){
			$isDeliveryConfirmation = false;
		}else{
			$isDeliveryConfirmation = true;
		}
		
		if($this->getConfigData('regular_pickup') == '0'){
			$isRegularPickUp = false;
		}else{
			$isRegularPickUp = true;
		}
		if($this->getConfigData('drop_off') == '0'){
			$isDropoff = false;
		}else{
			$isDropoff = true;
		}		
		
		$_rawRequest = array(
			//"ShipToResidential" => false,
			"ShipTo" => array(
				"ContactId" => "NOID",
				"CustomerId" => "",
				"UserId" => "",
				"ContactType" =>3,
				"CompanyName" => "",
				"FirstName" => "",
				"LastName" => "",
				"StreetAddress" => $request->getDestStreet()?$request->getDestStreet(): "",
				"ApartmentSuite" => "",
				"ProvinceRegion" => $request->getDestRegionCode()?$request->getDestRegionCode():"",
				"City" => $request->getDestCity()?$request->getDestCity():"",
				"State" => $request->getDestRegionCode()?$request->getDestRegionCode():"",
				"Country" => $request->getDestCountryId(),
				"Zip" => $request->getDestPostcode(),
				"TelephoneNo" => '',
				"FaxNo" => "",
				"Email" => "",
				"NickName" => "",
				"IsExpress" => false,
				"IsResidential" => false,
				"IsUserDefault" =>false,
				"UPSPickUpType" => 0,
				"TotalContacts" => "0"
			),
			"ShipFrom" => array(
				//"ContactId" => "NOID",
				"ContactId" => $contactId,
				"CustomerId" => "",
				"UserId" => "",
				"ContactType" => 3,
				"CompanyName" => "",
				"FirstName" => "",
				"LastName" => "",
				"StreetAddress" => "",
				"ApartmentSuite" => "",
				"ProvinceRegion" => "",
				"City" => "",
				"State" => "",
				"Country" => "",
				"Zip" => "",
				"TelephoneNo" => "",
				"FaxNo" => "",
				"Email" => "",
				"NickName" => "",
				"IsExpress" => false,
				"IsResidential" => false,
				"IsUserDefault" => false,
				"UPSPickUpType" => 0,
				"TotalContacts" => "0"
				),
			"Height" => $request->getPackageHeight()?$request->getPackageHeight(): 0,
			"Width" => $request->getPackageWidth()?$request->getPackageWidth():0,
			"Length" => $request->getPackageDepth()?$request->getPackageDepth():0,
			"Weight" => $request->getPackageWeight()?$request->getPackageWeight():0,
			"InsuredValue" => $insured,
			"IsSaturdayPickUp" => $isSaturdayPickUp,
			"IsSaturdayDelivery" => $isSaturdayDelivery,
			"IsDeliveryConfirmation" => $isDeliveryConfirmation,
			//"IsCod" => false,
			//"CodAmount" => 0.0,
			"PackageCode" => $this->getConfigData('packaging'),
			"IsSecuredCod" => false,
			"IsRegularPickUp" => $isRegularPickUp,
			"IsDropoff" => $isDropoff,
		);
		//Mage::log(json_encode($_rawRequest));
		return $_rawRequest;
	}

	private function _sendRequestShipment($requestPath){
		if(!$this->_sessionId){
			$this->_getSessionId();
		}
	
		if($this->_sessionId){
			$url = $this->_base_url . $this->_version . $requestPath . '?sessionID=' . $this->_sessionId;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/xml',
				'Content-Length: 0'
				)
			);
			
			$data = curl_exec ($ch);
			curl_close ($ch);
			if($data){
				$result = json_encode(simplexml_load_string($data));
				return json_decode($result, false);
			}else{
				return null;
			}
		}else{
			return null;
		}
	}
	private function _sendRequest($requestPath, $parrams, $isPost = false){
		if(!$this->_sessionId){
			$this->_getSessionId();
		}
		
		if($this->_sessionId){
			$url = $this->_base_url . $this->_version . $requestPath . '?sessionID=' . $this->_sessionId;
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
				if($requestPath == $this->_quote_path){
					//echo $url;
					//echo $data;
					//var_dump($data);
				}
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
		
		$username = Mage::helper('core')->decrypt($this->getConfigData('account'));
		$password = Mage::helper('core')->decrypt($this->getConfigData('password'));
		$apikey = Mage::helper('core')->decrypt($this->getConfigData('key'));
		
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

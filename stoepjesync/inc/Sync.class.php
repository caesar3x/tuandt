<?php
class Sync{
	protected $client = null;
	protected $session;
	protected $magentoDomain = '';
	protected $magentoUsername = '';
	protected $magentoAPIKey = '';
	
	public function init(){
		if(!$this->session){
			$db_stoepje = new Db_stoepje();
			$this->magentoDomain = $db_stoepje->magentoDomain;
			$this->magentoUsername = $db_stoepje->magentoUsername;
			$this->magentoAPIKey = $db_stoepje->magentoAPIKey;
			
			$this->client = new SoapClient($this->magentoDomain);
			// If somestuff requires api authentification,
			// we should get session token
			$this->session = $this->client->login($this->magentoUsername, $this->magentoAPIKey);			
		}
	}
	public function getAttributeSetList(){
		$attributeSets = $this->client->catalogProductAttributeSetList($this->session);
		return $attributeSets;
	}
	public function addProduct($type, $attributeSet, $sku, $productData, $storeView){
   		$productId = $this->client->catalogProductCreate($this->session, $type, $attributeSet, $sku, $productData);
   		return $productId;
	}
	public function updateProduct($sku, $productData){
		return $this->client->catalogProductUpdate($this->session, $sku, $productData);
	}
	public function deleteProduct($itemNo){
		return $this->client->catalogProductDelete($this->session, $itemNo);
	}
	public function getProduct($itemNo){
		return $this->client->catalogProductInfo($this->session, $itemNo, '', array(), 'sku');
	}
	
	public function createImage($productId, $imageData){
		return $this->client->catalogProductAttributeMediaCreate($this->session, $productId, $imageData);
	}
	public function updateImage($productId, $file, $imageData){
		return $this->client->catalogProductAttributeMediaUpdate($this->session, $productId, $file, $imageData);
	}
	
	public function getCategoryList(){
		$categoryList = $this->client->catalogCategoryTree($this->session, 3);
		return $categoryList;
	}
	public function getMediaList($productId){
		$mediaList = $this->client->catalogProductAttributeMediaList($this->session, $productId);
		return $mediaList;
	}
	public function removeMedia($productId, $file){
		$mediaList = $this->client->catalogProductAttributeMediaList($this->session, $productId, $file);
		return $mediaList;
	}
	public function listAllProduct(){
		$productList = $this->client->catalogProductList($this->session);
		return $productList;
	}
}
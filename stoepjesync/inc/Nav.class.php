<?php
class Nav{
	protected $conn = null;
	protected $productSkuList;
	protected $product;
	protected $productTierPriceList;
	protected $storeList;
	
	
	public function init(){
		if(!$this->conn){
			$db_stoepje = new Db_stoepje();
			$hostname = $db_stoepje->hostname;
			$dbname = $db_stoepje->dbname;
			$username = $db_stoepje->username;
			$pw = $db_stoepje->pw;
			
			//$this->conn = odbc_connect("Driver={SQL Server Native Client 10.0};Server=$hostname;Database=$dbname;", $username, $pw);
			//$this->conn = odbc_connect("MSSQL-PHP", $username, $pw);
			$this->conn = odbc_connect("SQLserver", $username, $pw);
			
		}
	}
	public function close(){
		odbc_close($this->conn);
	}
	
	public function getProductSkuList(){
		$this->productSkuList = array();
		$query = 'SELECT DISTINCT Id FROM [WEB_Dynamics01].[dbo].[Shop_Producten] AS product';
		$res = odbc_exec($this->conn,$query);
		while( $row = odbc_fetch_array($res) ) {
		    $this->productSkuList[] = $row;
		}
		return $this->productSkuList;
	}
	public function getProduct($sku){
		$query = 'SELECT * FROM [WEB_Dynamics01].[dbo].[Shop_Producten] AS product, [WEB_Dynamics01].[dbo].[Shop_Categorien] AS category WHERE product.Category = category.category_id AND Id=\''.$sku.'\' ORDER BY tierprice_data';
		$res = odbc_exec($this->conn,$query);
		while( $row = odbc_fetch_array($res) ) {
		    $this->product = $row;
		    break;
		}
		return $this->product;
	}
	public function getProductTierPriceList($id){
		$this->productTierPriceList = array();
		$query = 'SELECT * FROM [WEB_Dynamics01].[dbo].[Shop_Producten] AS product, [WEB_Dynamics01].[dbo].[Shop_Categorien] AS category WHERE product.Category = category.category_id AND tierprice_data > 1 AND id=\''.$id.'\'';
		$res = odbc_exec($this->conn,$query);
		while( $row = odbc_fetch_array($res) ) {
		    $this->productTierPriceList[] = $row;
		}
		return $this->productTierPriceList;
	}
	public function getStoreList(){
		$this->storeList = array();
		$query = 'SELECT * FROM [WEB_Dynamics01].[dbo].[Shop_markets] ORDER BY ID ';
		$res = odbc_exec($this->conn,$query);
		while( $row = odbc_fetch_array($res) ) {
		    $this->storeList[] = $row;
		}
		return $this->storeList;
	}
}
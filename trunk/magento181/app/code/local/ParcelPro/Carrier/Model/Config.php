<?php

class ParcelPro_Carrier_Model_Config extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('carrier/config');
    }
	
	public function deleteProduct($wholedata){
		$id = explode('/id/',$wholedata );
		Mage::register("isSecureArea", 1);
    	Mage :: app("default") -> setCurrentStore( Mage_Core_Model_App :: ADMIN_STORE_ID );
		Mage::getModel('catalog/product')->load($id[1])->delete();
		$collection=Mage::getModel('marketplace/product')->getCollection()
					->addFieldToFilter('mageproductid',array('eq'=>$id[1]));
		foreach($collection as $row){
			$row->delete();
		}
		return 0;
	}
}
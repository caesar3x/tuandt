<?php
class ParcelPro_Carrier_Adminhtml_RefreshController extends Mage_Adminhtml_Controller_Action {
	public function indexAction() {
		$this->getResponse ()->setRedirect ( $this->getUrl ( "admi\x6e\150\164\x6d\154\x2f\163\171\x73t\145\155_\x63\x6f\156\x66i\147\x2fe\144\x69\x74\057\x73\145c\x74i\157\x6e\x2f\x75\154\x74\151\155\157/" ) );
	}
	public function refreshAction() {
		$parcelProHelper = Mage::helper ( 'carrier/parcelpro' );
		$parcelProHelper->updateCarriers();
		$parcelProHelper->updateCarrierServices();		
		$parcelProHelper->updatePackageTypes();
		$parcelProHelper->updateSpecialServices();

		$this->getResponse ()->setRedirect ( $this->getUrl ( "adminhtml/system_config/edit/section/carriers" ) );
	}
}

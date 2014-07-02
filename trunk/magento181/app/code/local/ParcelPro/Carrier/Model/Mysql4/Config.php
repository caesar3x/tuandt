<?php
class ParcelPro_Carrier_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('carrier/config', 'index_id');
    }
}
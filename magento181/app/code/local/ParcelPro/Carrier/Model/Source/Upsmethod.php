<?php
/**
 
 * @category    Mage
 * @package     ParcelPro_Carrier
 * @copyright   Copyright (c) 2013 ParcelPro Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fedex method source implementation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class ParcelPro_Carrier_Model_Source_Upsmethod
{
    public function toOptionArray()
    {
        $parcelPro = Mage::getSingleton('carrier/carrier');
        $arr = array();
        $methods = $parcelPro->getCode('method', 'UPS');
        if($methods){
	        foreach ($methods as $k => $v) {
	            $arr[] = array('value' => $k, 'label' => $v);
	        }
        }
        return $arr;
    }
}

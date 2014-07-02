<?php
/**
 
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fedex packaging source implementation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class ParcelPro_Carrier_Model_Source_Unitofmeasure
{
    /**
     * Return array of Measure units
     *
     * @return array
     */
    public function toOptionArray()
    {
        $measureUnits = Mage::getSingleton('carrier/carrier')->getCode('unit_of_measure');
        $result = array();
        foreach ($measureUnits as $key => $val){
            $result[] = array('value'=>$key,'label'=>$val);
        }
        return $result;
    }
}

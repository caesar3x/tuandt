<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/25/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class ProductBrand extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __invoke($id = null)
    {
        if($id == null || $id == 0){
            return null;
        }
        $brandTable = $this->serviceLocator->get('BrandTable');
        return $brandTable->getNameById($id);
    }
    public function implement($id = null)
    {
        return $this->__invoke($id);
    }

    /**
     * @param $name
     * @return null
     */
    public function getBrandIdByName($name)
    {
        if(!$name){
            return null;
        }
        $brandTable = $this->serviceLocator->get('BrandTable');
        $entry = $brandTable->getEntryByName($name);
        if(!empty($entry)){
            return $entry->brand_id;
        }
    }
}
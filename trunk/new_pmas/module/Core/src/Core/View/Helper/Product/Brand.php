<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 11/2/13
 */
namespace Core\View\Helper\Product;

use Core\View\Helper\CoreHelper;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager;

class Brand extends CoreHelper
{
    public function __construct(ServiceManager $serviceLocator,Request $request)
    {
        parent::__construct($serviceLocator,$request);
    }
    public function getName($id = null)
    {
        if($id == null || $id == 0){
            return null;
        }
        $brandTable = $this->serviceLocator->get('BrandTable');
        return $brandTable->getNameById($id);
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
        return null;
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
class Type extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __invoke($id = null)
    {
        if(null == $id || $id == 0){
            return $id;
        }
        $typeTable = $this->serviceLocator->get('ProductTypeTable');
        return $typeTable->getTypeNameById($id);
    }
    public function implement($id = null)
    {
        return $this->__invoke($id);
    }

    /**
     * Get type name by id
     * @param $type_name
     * @return null
     */
    public function getTypeIdByName($type_name)
    {
        if(!$type_name){
            return null;
        }
        $typeTable = $this->serviceLocator->get('ProductTypeTable');
        $entry = $typeTable->getEntryByName($type_name);
        if(!empty($entry)){
            return $entry->type_id;
        }
        return null;
    }
}
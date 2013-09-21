<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
class ModelType extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __invoke($id = null)
    {
        if(null == $id){
            return $id;
        }
        $typeTable = $this->serviceLocator->get('DeviceTypeTable');
        return $typeTable->getTypeNameById($id);
    }
    public function implement($id = null)
    {
        return $this->__invoke($id);
    }
}
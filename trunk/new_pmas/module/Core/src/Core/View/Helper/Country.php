<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;

class Country extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __invoke($id = null)
    {
        if($id == null){
            return $id;
        }
        $countryTable = $this->serviceLocator->get('CountryTable');
        return $countryTable->getCountryNameById($id);
    }
    public function implement($id = null)
    {
        return $this->__invoke($id);
    }
}
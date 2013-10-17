<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;

class CountryHelper extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __invoke($id = null)
    {
        if($id == null || $id == 0){
            return $id;
        }
        $countryTable = $this->serviceLocator->get('CountryTable');
        return $countryTable->getCountryNameById($id);
    }
    public function implement($id = null)
    {
        return $this->__invoke($id);
    }

    /**
     * @param $country
     * @return null
     */
    public function getCountryNameById($country)
    {
        if(!$country){
            return null;
        }
        $countryTable = $this->serviceLocator->get('CountryTable');
        $entry = $countryTable->getEntryByName($country);
        if(!empty($entry)){
            return $entry->country_id;
        }
        return null;
    }

    /**
     * Get all available country record
     * @return mixed
     */
    public function getAvailableCountries()
    {
        $countryTable = $this->serviceLocator->get('CountryTable');
        return $countryTable->getAvaiableRows();
    }
}
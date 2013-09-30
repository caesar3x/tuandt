<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/28/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class Recycler extends AbstractHelper
{
    /**
     * @var $serviceLocator
     */
    protected $serviceLocator;

    /**
     * @param ServiceManager $serviceLocator
     */
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @param $recycler_id
     * @return null
     */
    public function getName($recycler_id)
    {
        if($recycler_id == null || $recycler_id == 0){
            return null;
        }
        $recyclerTable = $this->serviceLocator->get('RecyclerTable');
        $entry = $recyclerTable->getEntry($recycler_id);
        if(!empty($entry)){
            return $entry->name;
        }
        return $entry;
    }

    /**
     * @param $recycler_id
     * @return null
     */
    public function getCountryName($recycler_id)
    {
        if($recycler_id == null || $recycler_id == 0){
            return null;
        }
        $recyclerTable = $this->serviceLocator->get('RecyclerTable');
        $entry = $recyclerTable->getEntry($recycler_id);
        if(!empty($entry)){
            $country_id = $entry->country_id;
            $countryTable = $this->serviceLocator->get('CountryTable');
            if($countryTable->checkCountryAvailable($country_id)){
                $countryEntry = $countryTable->getEntry($country_id);
                if(!empty($countryEntry)){
                    return $countryEntry->name;
                }
            }else{
                return null;
            }
        }
        return $entry;
    }
    public function getCountryId($recycler_id)
    {
        if($recycler_id == null || $recycler_id == 0){
            return null;
        }
        $recyclerTable = $this->serviceLocator->get('RecyclerTable');
        $entry = $recyclerTable->getEntry($recycler_id);
        if(!empty($entry)){
            $country_id = $entry->country_id;
            $countryTable = $this->serviceLocator->get('CountryTable');
            if($countryTable->checkCountryAvailable($country_id)){
                return $country_id;
            }
            return 0;
        }
        return $entry;
    }

    /**
     * @return array
     */
    public function getListSelectCountry()
    {
        $countryTable = $this->serviceLocator->get('CountryTable');
        $countries = $countryTable->getAvaiableRows();
        $data = array(0 => 'Select Country');
        if(!empty($countries)){
            foreach($countries as $country){
                $data[$country->country_id] = $country->name;
            }
        }
        return $data;
    }

    /**
     * @param bool $includeSelect
     * @return array
     */
    public function getListSelectRecycler($includeSelect = true)
    {
        $recyclerTable = $this->serviceLocator->get('RecyclerTable');
        $recyclers = $recyclerTable->getAvaiableRows();
        if($includeSelect == true){
            $data = array(0 => 'Select Recycler');
        }else{
            $data = array();
        }
        if(!empty($recyclers)){
            foreach($recyclers as $recycler){
                $data[$recycler->recycler_id] = $recycler->name;
            }
        }
        return $data;
    }
    public function getRecyclerDetail($recycler_id)
    {
        if($recycler_id == null || $recycler_id == 0){
            return null;
        }
        $recyclerTable = $this->serviceLocator->get('RecyclerTable');
        $entry = $recyclerTable->getEntry($recycler_id);
        if(!empty($entry)){
            return $entry->name.' - '.$entry->address;
        }
        return $entry;
    }
}
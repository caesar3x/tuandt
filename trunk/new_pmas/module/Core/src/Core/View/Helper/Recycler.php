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
            $countryEntry = $countryTable->getEntry($country_id);
            if(!empty($countryEntry)){
                return $countryEntry->name;
            }
        }
        return $entry;
    }
}
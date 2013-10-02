<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/2/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class Resource extends AbstractHelper
{
    protected $serviceLocator;

    public $groupArray = array(
        'io' => 'Manage Login/Logout',
        'user' => 'Manage Users',
        'role' => 'Manage Roles',
        'resource' => 'Manage Resources',
        'tdm-product' => 'Manage TDM Products',
        'recycler' => 'Manage Recyclers',
        'recycler-product' => 'Manage Recycler Products',
        'country' => 'Manage Countries',
        'brand' => 'Manage Brands',
        'tdm-condition' => 'Manage TDM Conditions',
        'recycler-condition' => 'Manage Recycler Conditions',
        'exchange' => 'Manage Exchanges',
        'product-type' => 'Manage Product Types'
    );
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function parseGroupName($group)
    {
        if(array_key_exists($group,$this->groupArray)){
            return $this->groupArray[$group];
        }else{
            return null;
        }
    }
}
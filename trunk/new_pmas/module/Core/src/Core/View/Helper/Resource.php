<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/2/13
 */
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Resource extends AbstractHelper
{
    protected $serviceLocator;

    public $groupArray = array(
        'io' => 'Login/Logout',
        'user' => 'Manage Users',
        'tdm-product' => 'Manage TDM Products',
        'recycler-product' => 'Manage Recycler Products',
        'country' => 'Manage Countries',
        'brand' => 'Manage Brands',
        'tdm-condition' => 'Manage TDM Conditions',
        'recycler-condition' => 'Manage Recycler Conditions',
        'exchange' => 'Manage Exchange',
        'product-type' => 'Manage Product Types'
    );
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function parseGroupName($group)
    {

    }
}
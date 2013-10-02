<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/2/13
 */
namespace Application\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResourceForm extends Form
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __construct(ServiceLocatorInterface $sm,$n = 'resource')
    {
        $this->serviceLocator = $sm;
        parent::__construct($n);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $id = new Hidden('resource_id');
        $continue = new Hidden('continue');
        $continue->setValue('no');
        $continue->setAttribute('id','continue');
        $name = new Text('name');
        $name->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control'
        ));
        $path = new Text('path');
        $path->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control'
        ));
        $group = new Select('group');
        $group->setAttributes(array(
            'id' => 'group',
            'class' => 'form-control'
        ));
        $group->setValueOptions($this->getGroups());
        $user_define = new Hidden('user_define');
        $user_define->setValue(1);
        $csrf = new Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 600));
        $this->add($id)->add($name)->add($path)->add($continue)->add($user_define)->add($group)->add($csrf);
    }
    protected function getGroups()
    {
        $groupArray = array(
            0 => 'Select Group',
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
        return $groupArray;
    }
}
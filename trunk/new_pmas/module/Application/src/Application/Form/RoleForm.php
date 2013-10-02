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

class RoleForm extends Form
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __construct(ServiceLocatorInterface $sm,$n = 'role-form')
    {
        $this->serviceLocator = $sm;
        parent::__construct($n);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $id = new Hidden('role_id');
        $continue = new Hidden('continue');
        $continue->setValue('no');
        $continue->setAttribute('id','continue');
        $name = new Text('name');
        $name->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control'
        ));
        $role = new Text('role');
        $role->setAttributes(array(
            'id' => 'role',
            'class' => 'form-control'
        ));
        $resources = new Select('groups');
        $resources->setAttributes(array(
            'id' => 'groups',
            'class' => 'form-control chosen-select',
            'multiple' => 'multiple',
            'data-placeholder' => 'Choose Resources'
        ));
        $resources->setValueOptions($this->getGroups());
        $csrf = new Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 600));
        $this->add($id)->add($name)->add($role)->add($resources)->add($continue)->add($csrf);
    }

    /**
     * @return array
     */
    protected function getResources()
    {
        $resourcesTable = $this->serviceLocator->get('ResourcesTable');
        $rowset = $resourcesTable->getAvaiableResources();
        $data = array(0 => '');
        if(!empty($rowset)){
            foreach($rowset as $row){
                $data[$row->resource_id] = $row->name;
            }
        }
        return $data;
    }
    protected function getGroups()
    {
        $groupArray = array(
            'user' => 'Manage Users',
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
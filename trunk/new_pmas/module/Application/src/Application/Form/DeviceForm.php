<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Form;

use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;

class DeviceForm extends Form
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __construct(ServiceLocatorInterface $sm,$n = 'device')
    {
        $this->serviceLocator = $sm;
        parent::__construct($n);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $id = new Hidden('device_id');
        $continue = new Hidden('continue');
        $continue->setValue('no');
        $continue->setAttribute('id','continue');
        $model = new Text('model');
        $model->setAttributes(array(
            'id' => 'model',
            'class' => 'form-control'
        ));
        $brand = new Text('brand');
        $brand->setAttributes(array(
            'id' => 'brand',
            'class' => 'form-control'
        ));
        $type_id = new Select('type_id');
        $type_id->setAttributes(array(
            'id' => 'type_id',
            'class' => 'form-control'
        ));
    }
    public function getTypes()
    {

    }
}
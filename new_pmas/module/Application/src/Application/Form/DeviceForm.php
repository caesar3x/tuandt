<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Form;

use Zend\Form\Element\Csrf;
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
        $name = new Text('name');
        $name->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control'
        ));
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
        $type_id->setValueOptions($this->getTypes());
        $condition_id = new Select('condition_id');
        $condition_id->setAttributes(array(
            'id' => 'condition_id',
            'class' => 'form-control'
        ));
        $condition_id->setValueOptions($this->getDeviceConditions());
        $price = new Text('price');
        $price->setAttributes(array(
            'id' => 'price',
            'class' => 'form-control'
        ));
        $currency = new Select('currency');
        $currency->setAttributes(array(
            'id' => 'currency',
            'class' => 'form-control'
        ));
        $currency->setValueOptions(array(
            'HKD' => 'HKD',
            'USD' => 'USD'
        ));
        $csrf = new Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 600));
        $this->add($id)
            ->add($continue)
            ->add($name)
            ->add($model)
            ->add($brand)
            ->add($type_id)
            ->add($condition_id)
            ->add($price)
            ->add($currency)
            ->add($csrf)
            ;
    }
    public function getDeviceConditions($tdm = true)
    {
        if($tdm == true){
            $conditionTypeTable = $this->serviceLocator->get('TdmDeviceConditionTable');
        }else{
            $conditionTypeTable = $this->serviceLocator->get('RecyclerDeviceConditionTable');
        }
        $availableConditions = $conditionTypeTable->getAvaiableRows();
        $data = array(0 => 'Select Condition');
        if(!empty($availableConditions)){
            foreach($availableConditions as $row){
                $data[$row->condition_id] = $row->name;
            }
        }
        return $data;
    }
    public function getTypes()
    {
        $deviceTypeTable = $this->serviceLocator->get('DeviceTypeTable');
        $availableTypes = $deviceTypeTable->getAvaiableRows();
        $data = array(0 => 'Select Type');
        if(!empty($availableTypes)){
            foreach($availableTypes as $row){
                $data[$row->type_id] = $row->name;
            }
        }
        return $data;
    }
}
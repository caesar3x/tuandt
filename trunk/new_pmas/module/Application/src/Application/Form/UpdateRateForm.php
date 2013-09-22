<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/22/13
 */
namespace Application\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;

class UpdateRateForm extends Form
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __construct(ServiceLocatorInterface $sm,$n = 'update-exchange')
    {
        $this->serviceLocator = $sm;
        parent::__construct($n);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $continue = new Hidden('continue');
        $continue->setValue('no');
        $continue->setAttribute('id','continue');
        $exchange_rate = new Text('exchange_rate');
        $exchange_rate->setAttributes(array(
            'id' => 'currency',
            'class' => 'form-control'
        ));
        $currency = new Select('currency');
        $currency->setAttributes(array(
            'id' => 'currency',
            'class' => 'form-control'
        ));
        $time = new Text('time');
        $time->setAttributes(array(
            'id' => 'time',
            'class' => 'form-control datepicker'
        ));
        $currency->setValueOptions($this->getCurrencies());
        $csrf = new Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 600));
        $this->add($continue)->add($exchange_rate)->add($currency)->add($time)->add($csrf);
    }

    /**
     * Get list currencies
     * @return array
     */
    public function getCurrencies()
    {
        $countryTable = $this->serviceLocator->get('CountryTable');
        $data = array('none' => 'Select Currency');
        $availableCountries = $countryTable->getAvaiableRows();
        if(!empty($availableCountries)){
            foreach($availableCountries as $row){
                $data[$row->currency] = $row->currency;
            }
        }
        return $data;
    }
}
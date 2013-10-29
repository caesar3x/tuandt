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

class CountryForm extends Form
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __construct(ServiceLocatorInterface $sm,$n = 'country')
    {
        $this->serviceLocator = $sm;
        parent::__construct($n);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $id = new Hidden('country_id');
        $name = new Text('country_name');
        $name->setAttributes(array(
            'id' => 'country_name',
            'class' => 'form-control'
        ));
        $currency = new Text('currency');
        $currency->setAttributes(array(
            'id' => 'currency',
            'class' => 'form-control'
        ));
        $symbol = new Text('symbol');
        $symbol->setAttributes(array(
            'id' => 'symbol',
            'class' => 'form-control',
        ));
        $position = new Select('position');
        $position->setAttributes(array(
            'id' => 'position',
            'class' => 'form-control',
        ));
        $position->setValueOptions(array(
            'before' => 'Before',
            'after' => 'After'
        ));
        $csrf = new Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 3000));
        $this->add($id)->add($name)->add($symbol)->add($position)->add($currency)->add($csrf);
    }
}
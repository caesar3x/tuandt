<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/2/13
 */
namespace Application\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;

class PopularProduct extends Form
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __construct(ServiceLocatorInterface $sm,$n = 'popular-product')
    {
        $this->serviceLocator = $sm;
        parent::__construct($n);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $start = new Text('start');
        $start->setAttributes(array(
            'id' => 'start',
            'class' => 'datepicker form-control',
        ));
        $end = new Text('end');
        $end->setAttributes(array(
            'id' => 'end',
            'class' => 'datepicker form-control',
        ));
        $csrf = new Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 600));
        $this->add($start)->add($end)->add($csrf);
    }
}
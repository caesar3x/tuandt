<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/24/13
 */
namespace Application\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;

class BrandForm extends Form
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __construct(ServiceLocatorInterface $sm,$n = 'brand')
    {
        $this->serviceLocator = $sm;
        parent::__construct($n);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $id = new Hidden('brand_id');
        $name = new Text('name');
        $name->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control'
        ));
        $csrf = new Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 600));
        $this->add($id)->add($name)->add($csrf);
    }
}
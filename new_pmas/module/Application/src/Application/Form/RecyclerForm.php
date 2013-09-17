<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Form;

use Zend\Db\Sql\Ddl\Column\Text;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;

class RecyclerForm extends Form
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __construct($n = 'recycler',ServiceLocatorInterface $sm)
    {
        $this->serviceLocator = $sm;
        parent::__construct($n);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $id = new Hidden('id');
        $continue = new Hidden('continue');
        $continue->setValue('no');
        $continue->setAttribute('id','continue');
        $name = new Text('name');
        $name->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control'
        ));
        $name = new Select('country_id');
        $name->setAttributes(array(
            'id' => 'country_id',
            'class' => 'form-control'
        ));
        $this->add($id)
            ->add($continue)
            ;
    }
}
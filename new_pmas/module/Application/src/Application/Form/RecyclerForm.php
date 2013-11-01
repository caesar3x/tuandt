<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Email;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
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
        $id = new Hidden('recycler_id');
        $id->setAttribute('id' , 'recycler_id');
        $continue = new Hidden('continue');
        $continue->setValue('no');
        $continue->setAttribute('id','continue');
        $name = new Text('name');
        $name->setAttributes(array(
            'id' => 'name',
            'class' => 'form-control'
        ));
        $country = new Select('country_id');
        $country->setAttributes(array(
            'id' => 'country_id',
            'class' => 'form-control'
        ));
        $country->setValueOptions($this->getAvailableCountries());
        $email = new Email('email');
        $email->setAttributes(array(
            'id' => 'email',
            'class' => 'form-control'
        ));
        $telephone = new Text('telephone');
        $telephone->setAttributes(array(
            'id' => 'telephone',
            'class' => 'form-control'
        ));
        $website = new Text('website');
        $website->setAttributes(array(
            'id' => 'telephone',
            'class' => 'form-control'
        ));
        $address = new Textarea('address');
        $address->setAttributes(array(
            'id' => 'telephone',
            'class' => 'form-control'
        ));
        $csrf = new Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 3000));
        $this->add($id)
            ->add($continue)
            ->add($name)
            ->add($country)
            ->add($email)
            ->add($website)
            ->add($address)
            ->add($telephone)
            ->add($csrf)
            ;
    }

    /**
     * Get Available countries
     * @return array
     */
    public function getAvailableCountries()
    {
        $countryTable = $this->serviceLocator->get('CountryTable');
        $countries = $countryTable->getAvaiableRows();
        $data = array(0 => 'Select Country');
        if(!empty($countries)){
            foreach($countries as $c){
                $data[$c->country_id] = $c->name;
            }
        }
        return $data;
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/22/13
 */
namespace Application\Form;

use Core\Form\AbstractForm;
use Zend\Form\Element\Text;
use Zend\ServiceManager\ServiceLocatorInterface;

class SoapUserForm extends AbstractForm
{
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm,'soap-user-form');
        $username = new Text('username');
        $username->setAttributes(array(
            'id' => 'username',
            'class' => 'form-control'
        ));
        $password = new Text('password');
        $password->setAttributes(array(
            'id' => 'password',
            'class' => 'form-control'
        ));
        $this->add($username)->add($password);
    }
}
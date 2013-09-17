<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Form;
use Zend\Form\Form;
use Zend\Form\Element;
class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('login');
        $this->setAttribute('method', 'post');
        $email = new Element\Email('email');
        $email->setAttributes(array(
            'class' => 'form-control',
        ));
        $password = new Element\Password('password');
        $password->setAttributes(array(
            'class' => 'form-control',
        ));
        $csrf = new Element\Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 600));
        $this->add($email)
            ->add($password)
            ->add($csrf);
    }
}
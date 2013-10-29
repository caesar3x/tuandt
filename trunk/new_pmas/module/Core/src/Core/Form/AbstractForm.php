<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/27/13
 */
namespace Core\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractForm extends Form
{
    protected $serviceLocator;
    /**
     * @return \Zend\Uri\Uri
     */
    protected function getAction()
    {
        $currenUrl = $this->serviceLocator->get('viewhelpermanager')->get('CurrentUrl');
        return $currenUrl->getUri();
    }
    public function __construct(ServiceLocatorInterface $sm,$n = 'admin-form',$entry_id = 'id')
    {
        parent::__construct($n);
        $this->serviceLocator = $sm;
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', $this->getAction());
        $this->setAttribute('class', 'form-horizontal form-validate');
        $id = new Hidden($entry_id);
        $continue = new Hidden('continue');
        $continue->setValue('no');
        $continue->setAttribute('id','continue');
        $csrf = new Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 3000));
        $this->add($id)->add($continue)->add($csrf);
    }
}
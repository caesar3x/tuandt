<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Form;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserForm extends Form
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __construct($name = 'user',ServiceLocatorInterface $sm)
    {
        $this->serviceLocator = $sm;
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $id = new Element\Hidden('id');
        $continue = new Element\Hidden('continue');
        $continue->setValue('no');
        $continue->setAttribute('id','continue');
        $first_name = new Element\Text('first_name');
        $first_name->setAttributes(array(
            'id' => 'first-name',
            'class' => 'form-control'
        ));
        $last_name = new Element\Text('last_name');
        $last_name->setAttributes(array(
            'id' => 'last-name',
            'class' => 'form-control'
        ));
        $password = new Element\Password('password');
        $password->setAttributes(array(
            'id' => 'password',
            'class' => 'form-control'
        ));
        $email = new Element\Email('email');
        $email->setAttributes(array(
            'id' => 'email',
            'class' => 'form-control'
        ));
        $role = new Element\Select('role');
        $role->setAttributes(array(
            'id' => 'role',
            'class' => 'form-control'
        ));
        $role->setValueOptions($this->getRoles());
        $status = new Element\Select('status');
        $status->setAttributes(array(
            'id' => 'status',
            'class' => 'form-control'
        ));
        $status->setValueOptions(array(
            '0' => 'Disable',
            '1' => 'Activate',
        ));
        $note = new Element\Textarea('note');
        $note->setAttributes(array(
            'id' => 'note',
            'class' => 'form-control'
        ));
        $csrf = new Element\Csrf('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => 3000));
        $this->add($first_name)
            ->add($last_name)
            ->add($password)
            ->add($email)
            ->add($id)
            ->add($continue)
            ->add($role)
            ->add($status)
            ->add($note)
            ->add($csrf);
    }

    /**
     * Get avaiable roles
     * @return mixed
     */
    protected function getRoles()
    {
        $rolesTable = $this->serviceLocator->get('RolesTable');
        $avaiableRoles = $rolesTable->getAvaiableRoles();
        $data[0] = 'Select role';
        if(!empty($avaiableRoles)){
            foreach($avaiableRoles as $row){
                $data[$row->role_id] = $row->name;
            }
        }
        return $data;
    }
}
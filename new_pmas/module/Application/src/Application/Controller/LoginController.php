<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Controller;

use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Debug\Debug;
use Application\Form\LoginForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

class LoginController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $this->layout('layout/login');
        $form = new LoginForm();
        $flashMessenger = $this->flashMessenger();
        $msg = array();
        if ($flashMessenger->hasMessages()) {
            $msg = $flashMessenger->getMessages();
        }
        $view->setVariable('msg',$msg);
        $view->setVariable('form',$form);
        return $view;
    }
    public function authAction()
    {
        $request = $this->getRequest();
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $authAdapter = new AuthAdapter($dbAdapter,'admin_user','email','password');
        /*$authAdapter->setTableName('admin_user')->setIdentityColumn('email')->setCredentialColumn('password');*/
        $bcrypt = new Bcrypt();
        $bcrypt->setSalt(md5('vdragons'));
        $bcrypt->setCost(13);
        $securePass = $bcrypt->create($request->getPost('password'));
        $authAdapter
            ->setIdentity($request->getPost('email'))
            ->setCredential($securePass)
        ;
        $select = $authAdapter->getDbSelect();
        $select->where('status = 1 and hidden = 0 and deleted = 0');

        $authService = $this->getServiceLocator()->get('auth_service');
        $authService->setAdapter($authAdapter);
        $result = $authService->authenticate();
        if($result->isValid()){
            $storage = $authService->getStorage();
            $storage->write($authAdapter->getResultRowObject(array(
                'id',
                'username',
                'first_name',
                'last_name',
                'role',
                'email',
                'token',
                'created_at',
                'updated_at',
                'note'
            )));
            $storage->write($authAdapter->getResultRowObject(
                null,
                'password'
            ));
            /**
             * Log user
             */
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\login\\auth','Authenticate Success');
            $this->flashMessenger()->setNamespace('success')->addMessage('Login success.');
            return $this->redirect()->toUrl('/index');
        }else{
            $this->flashMessenger()->setNamespace('error')->addMessage('Login fail');
            return $this->redirect()->toUrl('/login');
        }
    }
}
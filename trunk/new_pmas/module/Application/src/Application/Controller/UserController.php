<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Controller;

use Application\Form\UserForm;
use Core\Model\AdminUser;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Db\NoRecordExists;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    protected $adminTable;

    public function auth()
    {
        $sm = $this->getServiceLocator();
        $authService = $sm->get('auth_service');
        if (! $authService->hasIdentity()) {
            return $this->redirect()->toUrl('/login');
        }
    }
    public function getMessages()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('messages');
    }

    public function indexAction()
    {
        $this->auth();
        $view = new ViewModel();
        if (!$this->adminTable) {
            $sm = $this->getServiceLocator();
            $this->adminTable = $sm->get('AdminUserTable');
            $admins = $this->adminTable->fetchAll();
            $view->setVariable('users',$admins);
        }
        return $view;
    }
    public function addAction()
    {
        $this->auth();
        $view = new ViewModel();
        return $view;
    }
    public function editAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $view = new ViewModel();
        $id = (int) $this->params('id',0);
        if(!$id || $id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        $form = new UserForm('user',$sm);
        if (!$this->adminTable) {
            $sm = $this->getServiceLocator();
            $this->adminTable = $sm->get('AdminUserTable');
        }
        $entry = $this->adminTable->getEntry($id);
        if(empty($entry)){
            $this->getResponse()->setStatusCode(404);
        }
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * check category name exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $email_valid = new NoRecordExists(array('table' => 'admin_user','field' => 'email','adapter' => $dbAdapter,'exclude' => array('field' => 'email','value' => $entry->email)));
            if(!$email_valid->isValid($post['email'])){
                $view->setVariable('msg',array('danger' => $messages['EMAIL_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()) {
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/user');
                }
                if($this->save($data)){
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('success' => $messages['UPDATE_SUCCESS']));
                        $view->setVariable('form',$form);
                        return $view;
                    }else{
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPDATE_SUCCESS']);
                        return $this->redirect()->toUrl('/user');
                    }
                }else{
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('danger' => $messages['UPDATE_FAIL']));
                        $view->setVariable('form',$form);
                        return $view;
                    }else{
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['UPDATE_FAIL']);
                        return $this->redirect()->toUrl('/user');
                    }
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->flashMessenger()->setNamespace('error')->addMessage($msg);
                }
                return $this->redirect()->toUrl('/user');
            }
        }else{
            $entryArray = (array) $entry;
            $form->setData($entryArray);
        }
        $view->setVariable('form',$form);
        return $view;
    }
    public function save($data)
    {
        $sm = $this->getServiceLocator();
        if(!$this->adminTable){
            $this->adminTable = $sm->get('AdminUserTable');
        }
        $success = true;
        $dataFinal = $data;
        $adminUser = new AdminUser();
        $adminUser->exchangeArray($dataFinal);
        $id = $adminUser->id;
        if($id != 0){
            $entry = $this->adminTable->getEntry($id);
            if(array_diff((array)$adminUser,(array)$entry) != null){
                $success = $success && $this->adminTable->save($adminUser);
            }
        }else{
            if($this->adminTable->save($adminUser)){
                $success = $success && true;
            }else{
                $success = $success && false;
            }
        }
        return $success;
    }
}
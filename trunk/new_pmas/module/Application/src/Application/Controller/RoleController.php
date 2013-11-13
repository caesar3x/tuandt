<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Controller;

use Application\Form\RoleForm;
use Core\Controller\AbstractController;
use Core\Model\Roles;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;

class RoleController extends AbstractController
{
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
        $rolesTable = $this->getServiceLocator()->get('RolesTable');
        $roles = $rolesTable->getAvaiableRows();
        $view->setVariable('roles',$roles);
        return $view;
    }
    public function addAction()
    {
        $this->auth();
        $view = new ViewModel();
        $rolesTable = $this->getServiceLocator()->get('RolesTable');
        $messages = $this->getMessages();
        $form = new RoleForm($this->getServiceLocator());
        $view->setVariable('form',$form);
        $request = $this->getRequest();
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['ROLE_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check recycler name exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $email_valid = new NoRecordExists(array('table' => 'roles','field' => 'name','adapter' => $dbAdapter));
            if(!$email_valid->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['ROLE_NAME_EXISTED']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/role');
                }
                if($this->saveRole($data)){
                    $lastInsertId = $rolesTable->getLastInsertValue();
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\role\\add',$messages['LOG_INSERT_ROLE_SUCCESS'].$lastInsertId);
                    $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['INSERT_SUCCESS']));
                    if($continue == 'yes'){
                        if($lastInsertId){
                            return $this->redirect()->toUrl('/role/edit/id/'.$lastInsertId);
                        }
                    }
                    return $this->redirect()->toUrl('/role');
                }else{
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\role\\add',$messages['LOG_INSERT_ROLE_FAIL']);
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['INSERT_FAIL']));
                    return $this->redirect()->toUrl('/role/add');
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\role\\add',$messages['LOG_INSERT_ROLE_FAIL']);
                    $view->setVariable('msg',array('danger' => $msg));
                }
                $view->setVariable('form',$form);
                return $view;
            }
        }
        return $view;
    }
    public function editAction()
    {
        $this->auth();
        $view = new ViewModel();
        $rolesTable = $this->getServiceLocator()->get('RolesTable');
        $messages = $this->getMessages();
        $form = new RoleForm($this->getServiceLocator());
        $view->setVariable('form',$form);
        $id = $this->params('id',0);
        if($id == 0 || (int) $id == 5){
            $this->getResponse()->setStatusCode(404);
        }
        $view->setVariable('id',$id);
        $entry = $rolesTable->getEntry($id);
        if(empty($entry)){
            $this->getResponse()->setStatusCode(404);
        }
        $view->setVariable('name',$entry->name);
        $request = $this->getRequest();
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['ROLE_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check recycler name exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $email_valid = new NoRecordExists(array('table' => 'roles','field' => 'name','adapter' => $dbAdapter,'exclude' => array('field' => 'name','value' => $entry->name)));
            if(!$email_valid->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['ROLE_NAME_EXISTED']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/role');
                }
                if($this->saveRole($data)){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\role\\edit',$messages['LOG_UPDATE_ROLE_SUCCESS'].$id);
                    $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['UPDATE_SUCCESS']));
                    if($continue == 'yes'){
                        if($id){
                            return $this->redirect()->toUrl('/role/edit/id/'.$id);
                        }
                    }
                    return $this->redirect()->toUrl('/role');
                }else{
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\role\\edit',$messages['LOG_UPDATE_ROLE_FAIL'].$id);
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['UPDATE_FAIL']));
                    return $this->redirect()->toUrl('/role/edit/id/'.$id);
                }
            }else{
                $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\role\\edit',$messages['LOG_UPDATE_ROLE_FAIL'].$id);
                foreach($form->getMessages() as $msg){
                    $view->setVariable('msg',array('danger' => $msg));
                }
                $view->setVariable('form',$form);
                return $view;
            }
        }else{
            $entryArray = (array) $entry;
            $entryArray['groups'] = unserialize($entry->resource_ids);
            $form->setData($entryArray);
        }
        return $view;
    }
    protected function saveRole($data)
    {
        $rolesTable = $this->getServiceLocator()->get('RolesTable');
        $dataFinale = $data;
        $viewhelperManager = $this->getServiceLocator()->get('viewhelpermanager');
        if($data['name'] && $data['name'] != ''){
            $dataFinale['role'] = $viewhelperManager->get('Slugify')->implement($data['name']);
        }
        if($data['groups']){
            $dataFinale['resource_ids'] = serialize($data['groups']);
        }
        $roles = new Roles();
        $roles->exchangeArray($dataFinale);
        return $rolesTable->save($roles);
    }
    public function deleteAction()
    {
        $this->auth();
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $id = $this->params('id',0);
        $rolesTable = $this->getServiceLocator()->get('RolesTable');
        if( (int) $id == 5)
        {
            return $this->redirect()->toUrl('/role');
        }
        if($id != 0){
            $this->deleteRole($id,$rolesTable);
        }
        if(!empty($ids) && is_array($ids)){
            foreach($ids as $id){
                $this->deleteRole($id,$rolesTable);
            }
        }
        return $this->redirect()->toUrl('/role');
    }
    protected function deleteRole($id,$table)
    {
        $messages = $this->getMessages();
        $result = $table->deleteEntry($id);
        if($result){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\role\\delete',$messages['LOG_DELETE_ROLE_SUCCESS'].$id);
            $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['DELETE_SUCCESS']));
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\role\\delete',$messages['LOG_DELETE_ROLE_FAIL'].$id);
            $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['DELETE_FAIL']));
        }
        return true;
    }
}
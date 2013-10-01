<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Controller;

use Application\Form\RoleForm;
use Core\Model\Roles;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;

class RoleController extends AbstractActionController
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/role');
                }
                if($this->saveRole($data)){
                    $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    if($continue == 'yes'){
                        $lastInsertId = $rolesTable->getLastInsertValue();
                        if($lastInsertId){
                            return $this->redirect()->toUrl('/role/edit/id/'.$lastInsertId);
                        }
                    }
                    return $this->redirect()->toUrl('/role');
                }else{
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['INSERT_FAIL']);
                    return $this->redirect()->toUrl('/role/add');
                }
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
        if($id == 0){
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/role');
                }
                if($this->saveRole($data)){
                    $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPDATE_SUCCESS']);
                    if($continue == 'yes'){
                        if($id){
                            return $this->redirect()->toUrl('/role/edit/id/'.$id);
                        }
                    }
                    return $this->redirect()->toUrl('/role');
                }else{
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['UPDATE_FAIL']);
                    return $this->redirect()->toUrl('/role/edit/id/'.$id);
                }
            }
        }else{
            $entryArray = (array) $entry;
            $entryArray['resources'] = unserialize($entry->resource_ids);
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
        if($data['resources']){
            $dataFinale['resource_ids'] = serialize($data['resources']);
        }
        $roles = new Roles();
        $roles->exchangeArray($dataFinale);
        return $rolesTable->save($roles);
    }
}
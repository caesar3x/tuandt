<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/13/13
 */
namespace Application\Controller;

use Application\Form\RecyclerForm;
use Core\Model\Recycler;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;

class RecyclerController extends AbstractActionController
{
    protected $recyclerTable;

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
        if (!$this->recyclerTable) {
            $sm = $this->getServiceLocator();
            $this->recyclerTable = $sm->get('RecyclerTable');
            $rowset = $this->recyclerTable->getAvaiableRows();
            $view->setVariable('rowset',$rowset);
        }
        return $view;
    }
    public function addAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $view = new ViewModel();
        $form = new RecyclerForm('recycler',$sm);
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['RECYCLER_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check recycler name exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $email_valid = new NoRecordExists(array('table' => 'recycler','field' => 'email','adapter' => $dbAdapter));
            if(!$email_valid->isValid($post['email'])){
                $view->setVariable('msg',array('danger' => $messages['EMAIL_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/recycler');
                }
                if($this->save($data)){
                    $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    if($continue == 'yes'){
                        $lastInsertId = $this->recyclerTable->getLastInsertValue();
                        if($lastInsertId){
                            return $this->redirect()->toUrl('/recycler/detail/id/'.$lastInsertId);
                        }
                    }
                    return $this->redirect()->toUrl('/recycler');
                }else{
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                        $view->setVariable('form',$form);
                        return $view;
                    }else{
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['INSERT_FAIL']);
                        return $this->redirect()->toUrl('/recycler');
                    }
                }
            }
        }
        $view->setVariable('form',$form);
        return $view;
    }
    public function detailAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $view = new ViewModel();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $view = new ViewModel();
        $id = (int) $this->params('id',0);
        if(!$id || $id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        $form = new RecyclerForm('recycler',$sm);
        if(!$this->recyclerTable){
            $this->recyclerTable = $sm->get('RecyclerTable');
        }
        $entry = $this->recyclerTable->getEntry($id);
        if(empty($entry)){
            $this->getResponse()->setStatusCode(404);
        }
        $view->setVariable('recycler',$entry->name);
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['RECYCLER_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check recycler name exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $email_valid = new NoRecordExists(array('table' => 'recycler','field' => 'email','adapter' => $dbAdapter,'exclude' => array('field' => 'email','value' => $entry->email)));
            if(!$email_valid->isValid($post['email'])){
                $view->setVariable('msg',array('danger' => $messages['EMAIL_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()) {
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/recycler');
                }
                if($this->save($data)){
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('success' => $messages['UPDATE_SUCCESS']));
                        $view->setVariable('form',$form);
                        return $view;
                    }else{
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPDATE_SUCCESS']);
                        return $this->redirect()->toUrl('/recycler');
                    }
                }else{
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('danger' => $messages['UPDATE_FAIL']));
                        $view->setVariable('form',$form);
                        return $view;
                    }else{
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['UPDATE_FAIL']);
                        return $this->redirect()->toUrl('/recycler');
                    }
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->flashMessenger()->setNamespace('error')->addMessage($msg);
                }
                return $this->redirect()->toUrl('/recycler');
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
        if(!$this->recyclerTable){
            $this->recyclerTable = $sm->get('RecyclerTable');
        }
        $success = true;
        $dataFinal = $data;
        $recycler = new Recycler();
        $recycler->exchangeArray($dataFinal);
        $id = $recycler->recycler_id;
        if($id != 0){
            $entry = $this->recyclerTable->getEntry($id);
            if(array_diff((array)$recycler,(array)$entry) != null){
                $success = $success && $this->recyclerTable->save($recycler);
            }
        }else{
            if($this->recyclerTable->save($recycler)){
                $success = $success && true;
            }else{
                $success = $success && false;
            }
        }
        return $success;
    }
}
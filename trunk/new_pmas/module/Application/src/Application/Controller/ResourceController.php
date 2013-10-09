<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Controller;

use Application\Form\ResourceForm;
use Core\Model\Resources;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;

class ResourceController extends AbstractActionController
{
    protected $resourcesTable;

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
        if (!$this->resourcesTable) {
            $sm = $this->getServiceLocator();
            $this->resourcesTable = $sm->get('ResourcesTable');
            $rowset = $this->resourcesTable->getAvaiableResources();
            $view->setVariable('rowset',$rowset);
        }
        return $view;
    }
    public function addAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $view = new ViewModel();
        $request = $this->getRequest();
        $form = new ResourceForm($this->getServiceLocator());
        $view->setVariable('form',$form);
        $resourceTable = $this->getServiceLocator()->get('ResourcesTable');
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            $select_empty = new NotEmpty(array('integer','zero'));
            if(!$select_empty->isValid($post['group'])){
                $view->setVariable('msg',array('danger' => $messages['RESOURCE_GROUP_NOT_SELECTED']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['RESOURCE_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check recycler name exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $email_valid = new NoRecordExists(array('table' => 'resources','field' => 'path','adapter' => $dbAdapter));
            if(!$email_valid->isValid($post['path'])){
                $view->setVariable('msg',array('danger' => $messages['PATH_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/resource');
                }
                if($this->saveResource($data)){
                    $lastInsertId = $resourceTable->getLastInsertValue();
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\resource\\add',$messages['LOG_INSERT_RESOURCE_SUCCESS'].$lastInsertId);
                    $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    if($continue == 'yes'){

                        if($lastInsertId){
                            return $this->redirect()->toUrl('/resource/edit/id/'.$lastInsertId);
                        }
                    }
                    return $this->redirect()->toUrl('/resource');
                }else{
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\resource\\add',$messages['LOG_INSERT_RESOURCE_FAIL']);
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['INSERT_FAIL']);
                    return $this->redirect()->toUrl('/resource/add');
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\resource\\add',$messages['LOG_INSERT_RESOURCE_FAIL']);
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
        $messages = $this->getMessages();
        $view = new ViewModel();
        $request = $this->getRequest();
        $form = new ResourceForm($this->getServiceLocator());
        $view->setVariable('form',$form);
        $resourceTable = $this->getServiceLocator()->get('ResourcesTable');
        $id = $this->params('id',0);
        if($id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        $view->setVariable('id',$id);
        $entry = $resourceTable->getEntry($id);
        if(empty($entry)){
            $this->getResponse()->setStatusCode(404);
        }
        $view->setVariable('name',$entry->name);
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            $select_empty = new NotEmpty(array('integer','zero'));
            if(!$select_empty->isValid($post['group'])){
                $view->setVariable('msg',array('danger' => $messages['RESOURCE_GROUP_NOT_SELECTED']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['RESOURCE_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check recycler name exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $email_valid = new NoRecordExists(array('table' => 'resources','field' => 'path','adapter' => $dbAdapter,'exclude' => array('field' => 'path','value' => $entry->path)));
            if(!$email_valid->isValid($post['path'])){
                $view->setVariable('msg',array('danger' => $messages['PATH_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/resource');
                }
                if($this->saveResource($data)){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\resource\\edit',$messages['LOG_UPDATE_RESOURCE_SUCCESS'].$id);
                    $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPDATE_SUCCESS']);
                    if($continue == 'yes'){
                        return $this->redirect()->toUrl('/resource/edit/id/'.$id);
                    }
                    return $this->redirect()->toUrl('/resource');
                }else{
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\resource\\edit',$messages['LOG_UPDATE_RESOURCE_FAIL'].$id);
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['UPDATE_FAIL']);
                    return $this->redirect()->toUrl('/resource/edit/id/'.$id);
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\resource\\edit',$messages['LOG_UPDATE_RESOURCE_FAIL'].$id);
                    $view->setVariable('msg',array('danger' => $msg));
                }
                $view->setVariable('form',$form);
                return $view;
            }
        }else{
            $entryArray = (array) $entry;
            $form->setData($entryArray);
        }
        $view->setVariable('form',$form);
        return $view;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function saveResource($data)
    {
        $resourceTable = $this->getServiceLocator()->get('ResourcesTable');
        $dataFinale = $data;
        $resource = new Resources();
        $resource->exchangeArray($dataFinale);
        return $resourceTable->save($resource);
    }
    public function deleteAction()
    {
        $this->auth();
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $id = $this->params('id',0);
        $resourceTable = $this->getServiceLocator()->get('ResourcesTable');
        if($id != 0){
            $this->deleteResource($id,$resourceTable);
        }
        if(!empty($ids) && is_array($ids)){
            foreach($ids as $id){
                $this->deleteResource($id,$resourceTable);
            }
        }
        return $this->redirect()->toUrl('/resource');
    }

    /**
     * @param $id
     * @param $table
     * @return bool
     */
    protected function deleteResource($id,$table)
    {
        $messages = $this->getMessages();
        $result = $table->deleteEntry($id);
        if($result){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\resource\\delete',$messages['LOG_DELETE_RESOURCE_SUCCESS'].$id);
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\resource\\delete',$messages['LOG_DELETE_RESOURCE_FAIL'].$id);
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
        return true;
    }
}
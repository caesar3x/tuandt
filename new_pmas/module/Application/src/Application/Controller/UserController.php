<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Controller;

use Application\Form\UserForm;
use Core\Model\AdminUser;
use Zend\Crypt\Password\Bcrypt;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;
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
            $admins = $this->adminTable->getAvaiableUsers();
            $view->setVariable('users',$admins);
        }
        return $view;
    }
    public function addAction()
    {
        $this->auth();
        $view = new ViewModel();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $form = new UserForm('user',$sm);
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['password'])){
                $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\add',$messages['LOG_USER_INSERT_FAIL'].' : '.$messages['PASSWORD_NOT_EMPTY']);
                $view->setVariable('msg',array('danger' => $messages['PASSWORD_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check category name exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $email_valid = new NoRecordExists(array('table' => 'admin_user','field' => 'email','adapter' => $dbAdapter));
            if(!$email_valid->isValid($post['email'])){
                $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\add',$messages['LOG_USER_INSERT_FAIL'].' : '.$messages['EMAIL_EXIST']);
                $view->setVariable('msg',array('danger' => $messages['EMAIL_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()) {
                $data = $form->getData();
                if(empty($data)){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\add',$messages['LOG_USER_INSERT_FAIL'].' : '.$messages['NO_DATA']);
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/user');
                }
                if($this->save($data)){
                    $lastInsertId = $this->adminTable->getLastInsertValue();
                    $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\add',$messages['LOG_USER_INSERT_SUCCESS'].$lastInsertId);
                }else{
                    $view->setVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                    $view->setVariable('form',$form);
                    /**
                     * Log user
                     */
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\add',$messages['LOG_USER_INSERT_FAIL']);
                    return $view;
                }
                if($continue == 'yes'){
                    if($lastInsertId){
                        return $this->redirect()->toUrl('/user/detail/id/'.$lastInsertId);
                    }
                }
                return $this->redirect()->toUrl('/user');
            }else{
                foreach($form->getMessages() as $msg){
                    /**
                     * Log user
                     */
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\add',$msg);
                    $view->setVariable('msg',array('danger' => $msg));
                }
                $view->setVariable('form',$form);
                return $view;
            }
        }
        $view->setVariable('form',$form);
        return $view;
    }
    public function detailAction()
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
                $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\detail',$messages['LOG_USER_UPDATE_FAIL'].$id.' : '.$messages['EMAIL_EXIST']);
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
                    $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPDATE_SUCCESS']);
                    /**
                     * Log user
                     */
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\detail',$messages['LOG_USER_UPDATE_SUCCESS'].$id);
                    if($continue == 'yes'){
                        return $this->redirect()->toUrl('/user/detail/id/'.$id);
                    }else{
                        return $this->redirect()->toUrl('/user');
                    }
                }else{
                    /**
                     * Log user
                     */
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\detail',$messages['LOG_USER_UPDATE_FAIL'].$id);
                    $view->setVariable('msg',array('danger' => $messages['UPDATE_FAIL']));
                    $view->setVariable('form',$form);
                    return $view;

                }
            }else{
                foreach($form->getMessages() as $msg){
                    $view->setVariable('msg',array('danger' => $msg));
                    /**
                     * Log user
                     */
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\detail',$msg);
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
    public function save($data)
    {
        $sm = $this->getServiceLocator();
        if(!$this->adminTable){
            $this->adminTable = $sm->get('AdminUserTable');
        }
        $success = true;
        if(isset($data['password']) && $data['password'] != ''){
            $bcrypt = new Bcrypt();
            $bcrypt->setSalt(md5('vdragons'));
            $bcrypt->setCost(13);
            $data['password'] = $bcrypt->create($data['password']);
        }
        $dataFinal = $data;
        $adminUser = new AdminUser();
        $adminUser->exchangeArray($dataFinal);
        $id = $adminUser->id;
        if($id != 0){
            $entry = $this->adminTable->getEntry($id);
            if($data['password'] == ''){
                $data['password'] = $entry->password;
            }
            $adminUser->exchangeArray($data);
            $success = $success && $this->adminTable->save($adminUser);
        }else{
            if($this->adminTable->save($adminUser)){
                $success = $success && true;
            }else{
                $success = $success && false;
            }
        }
        return $success;
    }
    public function deleteAction()
    {
        $this->auth();
        $id = $this->params('id',0);
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        if(!$this->adminTable){
            $this->adminTable = $this->serviceLocator->get('AdminUserTable');
        }
        if($id != 0){
            $this->delete($id,$this->adminTable);
        }
        if(!empty($ids) && is_array($ids)){
            foreach($ids as $id){
                $this->delete($id,$this->adminTable);
            }
        }
        return $this->redirect()->toUrl('/user');
    }
    protected function delete($id,$table)
    {
        $messages = $this->getMessages();
        if($table->deleteEntry($id)){
            /**
             * Log user
             */
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\delete',$messages['LOG_USER_DELETE_SUCCESS'].$id);
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            /**
             * Log user
             */
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\delete',$messages['LOG_USER_DELETE_FAIL'].$id);
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
        /*return $this->redirect()->toUrl('/user');*/
    }
    public function viewLogAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $id = $this->params('id',0);
        if($id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        /**
         * Log user
         */
        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\view-log',$messages['LOG_USER_VIEW_LOG'].$id);
        $usermetaTable = $this->getServiceLocator()->get('UsermetaTable');
        $logs = $usermetaTable->getUserLog($id);
        $view = new ViewModel();
        $view->setVariable('logs',$logs);
        $view->setVariable('id',$id);
        return $view;
    }
    public function deleteLogAction()
    {
        $this->auth();
        $id = $this->params('id',0);
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $user = $this->params('user',0);
        $usermetaTable = $this->getServiceLocator()->get('UsermetaTable');
        if($id != 0){
            $this->deleteLog($id,$usermetaTable,$user);
        }
        if(!empty($ids) && is_array($ids)){
            foreach($ids as $id){
                $this->deleteLog($id,$usermetaTable,$user);
            }
        }
        if($user == 0){
            return $this->redirect()->toUrl('/user');
        }else{
            return $this->redirect()->toUrl('/user/view-log/user/id/'.$user);
        }

    }
    protected function deleteLog($id,$table,$user)
    {
        $messages = $this->getMessages();
        if($table->deleteEntry($id)){
            /**
             * Log user
             */
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\delete-log',$messages['LOG_USER_DELETE_LOG_SUCCESS'].$user);
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            /**
             * Log user
             */
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\user\\delete-log',$messages['LOG_USER_DELETE_LOG_FAIL'].$user);
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
    }
}
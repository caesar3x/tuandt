<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/22/13
 */
namespace Application\Controller;

use Application\Form\SoapUserForm;
use Core\Controller\AbstractController;
use Core\Model\SoapUsers;
use Core\Soap\Server1;
use Core\Soap\Server2;
use Zend\Debug\Debug;
use Zend\Soap\AutoDiscover;
use Zend\Soap\Client;
use Zend\Soap\Server;
use Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;

class SoapController extends AbstractController
{
    public function indexAction()
    {
        echo $this->getViewHelperPlugin('files')->getUploadLanguageFolderPath();
        /*$server1 = new Server2();
        $server1->login('dat','123456');
        $result = $server1->update(25,array('product_name' => 'HTC Nexus 10','price' => 43243,'type_id'=>110,'model' => 'modeltes'));
        Debug::dump($result);
        die;*/

        /*$ret = $client->create(array('product_name' => 'HTC Nexus 10s','base_price' => 34,'type_id'=>100));*/
        /*$data = $client->setPrice(array('model' => 'Galaxy SI','price' => 44,'currency' => 'USD','datetime' => '30-10-2013'));*/
        /*$ret = $client->create(array('product_model' => 'Model Test','product_name' => 'Name Test','base_price' => 123));*/
        /*foreach($data as $row){
            Debug::dump($row);
        }*/

        /*$wsdl = 'http://pmas.dev.gyhk.com/soap/recycler-product?wsdl';


        $client = new \Zend\Soap\Client($wsdl);
        $client->login("dat", "123456");
        $ret = $client->delete(1);
        Debug::dump($ret);*/

        die;
    }
    public function recyclerProductAction()
    {
        $current = $this->getServiceLocator()->get('viewhelpermanager')->get('currentUrl')->getPath();
        $uri = $current."?wsdl";
        if (isset($_GET['wsdl'])) {
            $server = new \Zend\Soap\AutoDiscover(new \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType());
            $server->setUri($current);
        } else {
            $server = new \Zend\Soap\Server($uri);
        }
        $server->setClass('Core\Soap\Server1');
        $server->handle();
        exit();
    }
    public function tdmProductAction()
    {
        $current = $this->getServiceLocator()->get('viewhelpermanager')->get('currentUrl')->getPath();
        $uri = $current."?wsdl";
        if (isset($_GET['wsdl'])) {
            $server = new \Zend\Soap\AutoDiscover(new \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType());
            $server->setUri($current);
        } else {
            $server = new \Zend\Soap\Server($uri);
        }
        $server->setClass('Core\Soap\Server2');
        $server->handle();
        exit();
    }
    /**
     * START PROCESSING SOAP USERS
     */
    public function userAction()
    {
        parent::initAction();
        $soapUsersTable = $this->sm->get('SoapUsersTable');
        $rowset = $soapUsersTable->getAvaiableRows();
        $this->setViewVariable('rowset',$rowset);
        return $this->view;
    }
    public function addUserAction()
    {
        parent::initAction();
        $form  = new SoapUserForm($this->sm);
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $soapUsersTable = $this->sm->get('SoapUsersTable');
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['username'])){
                $this->getViewHelperPlugin('user')->log('application\\soap\\add-user',$messages['LOG_INSERT_SOAP_USER_FAIL'].' : '.$messages['USERNAME_NOT_EMPTY']);
                $this->setViewVariable('msg',array('danger' => $messages['USERNAME_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if(!$empty->isValid($post['password'])){
                $this->getViewHelperPlugin('user')->log('application\\soap\\add-user',$messages['LOG_INSERT_SOAP_USER_FAIL'].' : '.$messages['PASSWORD_NOT_EMPTY']);
                $this->setViewVariable('msg',array('danger' => $messages['PASSWORD_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            /**
             * check exist username
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $username_valid = new NoRecordExists(array('table' => 'soap_users','field' => 'username','adapter' => $dbAdapter));
            if(!$username_valid->isValid($post['username'])){
                $this->getViewHelperPlugin('user')->log('application\\soap\\add-user',$messages['LOG_INSERT_SOAP_USER_FAIL'].' : '.$messages['USERNAME_EXISTED']);
                $this->setViewVariable('msg',array('danger' => $messages['USERNAME_EXISTED']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirectUrl('/soap/user');
                }
                if($this->saveUser($data)){
                    $id = $soapUsersTable->getLastInsertValue();
                    $this->getViewHelperPlugin('user')->log('application\\soap\\add-user',$messages['LOG_INSERT_SOAP_USER_SUCCESS'].$id);
                    $this->addSuccessFlashMessenger($this->__($messages['INSERT_SUCCESS']));
                    if($continue == 'yes'){

                        return $this->redirectUrl('/soap/edit-user/id/'.$id);
                    }else{
                        return $this->redirect()->toUrl('/soap/user');
                    }
                }else{
                    $this->getViewHelperPlugin('user')->log('application\\soap\\add-user',$messages['LOG_INSERT_SOAP_USER_FAIL']);
                    $this->setViewVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                    $this->setViewVariable('form',$form);
                    return $this->view;
                }
            }else{
                $this->getViewHelperPlugin('user')->log('application\\soap\\add-user',$messages['LOG_INSERT_SOAP_USER_FAIL']);
                $this->setViewVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
        }
        $this->setViewVariable('form',$form);
        return $this->view;
    }
    protected function saveUser($data)
    {
        $sm = $this->getServiceLocator();
        $soapUsersTable = $sm->get('SoapUsersTable');
        $dataFinal = $data;
        $soapUsers = new SoapUsers();
        $soapUsers->exchangeArray($dataFinal);
        return $soapUsersTable->save($soapUsers);
    }
    public function editUserAction()
    {
        parent::initAction();
        $form  = new SoapUserForm($this->sm);
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $soapUsersTable = $this->sm->get('SoapUsersTable');
        $id = $this->params('id',0);
        if(!$id || $id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        $entry = $soapUsersTable->getEntry($id);
        if(empty($entry)){
            $this->getResponse()->setStatusCode(404);
        }
        $this->setViewVariable('id',$id);
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['username'])){
                $this->getViewHelperPlugin('user')->log('application\\soap\\edit-user',$messages['LOG_UPDATE_SOAP_USER_FAIL'].$id.' : '.$messages['USERNAME_NOT_EMPTY']);
                $this->setViewVariable('msg',array('danger' => $messages['USERNAME_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if(!$empty->isValid($post['password'])){
                $this->getViewHelperPlugin('user')->log('application\\soap\\edit-user',$messages['LOG_UPDATE_SOAP_USER_FAIL'].$id.' : '.$messages['USERNAME_NOT_EMPTY']);
                $this->setViewVariable('msg',array('danger' => $messages['PASSWORD_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            /**
             * check exist username
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $username_valid = new NoRecordExists(array('table' => 'soap_users','field' => 'username','adapter' => $dbAdapter,'exclude' => array('field' => 'username','value' => $entry->username)));
            if(!$username_valid->isValid($post['username'])){
                $this->getViewHelperPlugin('user')->log('application\\soap\\edit-user',$messages['LOG_UPDATE_SOAP_USER_FAIL'].$id.' : '.$messages['USERNAME_EXISTED']);
                $this->setViewVariable('msg',array('danger' => $messages['USERNAME_EXISTED']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirectUrl('/soap/user');
                }
                if($this->saveUser($data)){
                    $this->getViewHelperPlugin('user')->log('application\\soap\\edit-user',$messages['LOG_UPDATE_SOAP_USER_SUCCESS'].$id);
                    $this->addSuccessFlashMessenger($this->__($messages['UPDATE_SUCCESS']));
                    if($continue == 'yes'){
                        $this->setViewVariable('msg',array('success' => $messages['UPDATE_SUCCESS']));
                        $this->setViewVariable('form',$form);
                        return $this->view;
                    }else{
                        return $this->redirect()->toUrl('/soap/user');
                    }
                }else{
                    $this->getViewHelperPlugin('user')->log('application\\soap\\edit-user',$messages['LOG_UPDATE_SOAP_USER_SUCCESS'].$id);
                    $this->setViewVariable('msg',array('danger' => $messages['UPDATE_FAIL']));
                    $this->setViewVariable('form',$form);
                    return $this->view;
                }
            }else{
                $this->getViewHelperPlugin('user')->log('application\\soap\\edit-user',$messages['LOG_UPDATE_SOAP_USER_SUCCESS'].$id);
                $this->setViewVariable('msg',array('danger' => $messages['UPDATE_FAIL']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
        }else{
            $entryArray = (array) $entry;
            $form->setData($entryArray);
        }
        $this->setViewVariable('form',$form);
        return $this->view;
    }
    public function deleteUserAction()
    {
        parent::initAction();
        $id = $this->params('id',0);
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $soapUsersTable = $this->sm->get('SoapUsersTable');
        if($id != 0){
            $this->deleteUser($id,$soapUsersTable);
        }else{
            if(!empty($ids) && is_array($ids)){
                foreach($ids as $id){
                    $this->deleteUser($id,$soapUsersTable);
                }
            }
        }
        return $this->redirect()->toUrl('/soap/user');
    }
    protected function deleteUser($id,$table)
    {
        $messages = $this->getMessages();
        if($table->deleteEntry($id)){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\soap\\delete-user',$messages['LOG_DELETE_SOAP_USER_SUCCESS'].$id);
            $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['DELETE_SUCCESS']));
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\soap\\delete-user',$messages['LOG_DELETE_SOAP_USER_FAIL'].$id);
            $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['DELETE_FAIL']));
        }
        return true;
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Controller;

use Application\Form\UserForm;
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
        $id = $this->params('id',0);
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
                $id = (int) $data['id'];
                $entryTerm = $this->adminTable->getEntry($id);
                Debug::dump($data);
                die('valid');
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
        $messages = $this->getMessages();
        $sm = $this->getServiceLocator();
        $viewhelpermanager = $sm->get('viewhelpermanager');
        if(!$this->adminTable){
            $this->adminTable = $sm->get('AdminUserTable');
        }
        $success = true;
        $dataFinal = $data;
        if($data['name'] && $data['slug'] == ''){
            $nameSlug = $viewhelpermanager->get('slugify')->implement($data['name']);
            $dataFinal['slug'] = $nameSlug;
        }
        $dataFinal['taxonomy'] = 'category';
        $term = new Terms();
        $term->exchangeArray($dataFinal);
        $term_id = $term->term_id;
        $lastestInsertId = $term_id;
        if($term_id != null){
            $entry = $this->termsTable->getEntry($term_id);
            if(array_diff((array)$term,(array)$entry) != null){
                $success = $success && $this->termsTable->save($term);
            }
        }else{
            if($this->termsTable->save($term)){
                $success = $success && true;
                $lastestInsertId = $this->termsTable->getLastInsertValue();
            }else{
                $success = $success && false;
            }
        }
        if(isset($lastestInsertId)){
            $dataFinal['term_id'] = $lastestInsertId;
            $termTaxonomyEntry = $this->termTaxonomyTable->getByTermId($lastestInsertId);
            if($termTaxonomyEntry != null){
                $dataFinal['term_taxonomy_id'] = $termTaxonomyEntry->term_taxonomy_id;
            }
            if($data['thumbnail']['name'] && trim($data['thumbnail']['name']) != ''){
                if (!file_exists($path .DIRECTORY_SEPARATOR . $data['thumbnail']['name'])) {
                    move_uploaded_file($data['thumbnail']['tmp_name'], $path .DIRECTORY_SEPARATOR .$data['thumbnail']['name'] );
                    $dataFinal['thumbnail'] = '/upload/catalog/category/' .$data['thumbnail']['name'];
                }else{
                    $thumbInfo = pathinfo($data['thumbnail']['name']);
                    move_uploaded_file($data['thumbnail']['tmp_name'], $path .DIRECTORY_SEPARATOR .$thumbInfo['filename'].'_'.$lastestInsertId.'.'.$thumbInfo['extension'] );
                    $dataFinal['thumbnail'] = '/upload/catalog/category/' .$thumbInfo['filename'].'_'.$lastestInsertId.'.'.$thumbInfo['extension'];
                }
            }else{
                $dataFinal['thumbnail'] = $data['thumbnail_mask'];
            }
            if($data['image']['name'] && trim($data['image']['name']) != ''){
                if (!file_exists($path .DIRECTORY_SEPARATOR . $data['image']['name'])) {
                    move_uploaded_file($data['image']['tmp_name'], $path .DIRECTORY_SEPARATOR .$data['image']['name'] );
                    $dataFinal['image'] = '/upload/catalog/category/' .$data['image']['name'];
                }else{
                    $imageInfo = pathinfo($data['image']['name']);
                    move_uploaded_file($data['image']['tmp_name'], $path .DIRECTORY_SEPARATOR .$imageInfo['filename'].'_'.$lastestInsertId.'.'.$imageInfo['extension'] );
                    $dataFinal['image'] = '/upload/catalog/category/' .$imageInfo['filename'].'_'.$lastestInsertId.'.'.$imageInfo['extension'] ;
                }
            }else{
                $dataFinal['image'] = $data['image_mask'];
            }
            $termTaxonomy = new TermTaxonomy();
            $termTaxonomy->exchangeArray($dataFinal);
            if(array_diff((array)$termTaxonomy,(array)$termTaxonomyEntry) != null){
                $success = $success && $this->termTaxonomyTable->save($termTaxonomy);
            }
        }
        return $success;
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/13/13
 */
namespace Application\Controller;

use Application\Form\ProductForm;
use BasicExcel\Writer\Csv;
use BasicExcel\Writer\Xls;
use BasicExcel\Writer\Xlsx;
use Core\Model\Product;
use Core\Model\TdmProduct;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Digits;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;

class ProductController extends AbstractActionController
{
    protected $productTable;

    protected $tdmProductTable;

    protected $recyclerProductTable;

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
        if (!$this->productTable) {
            $sm = $this->getServiceLocator();
            $this->productTable = $sm->get('ProductTable');
            $rowset = $this->productTable->getAvaiableTdmProducts();
            $view->setVariable('rowset',$rowset);
        }
        return $view;
    }
    public function tdmAction()
    {
        $this->auth();
        $view = new ViewModel();
        return $view;
    }
    public function addTdmAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $form = new ProductForm($sm);
        $view = new ViewModel();
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['brand'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_BRAND_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['model'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_MODEL_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/product');
                }
                if($this->save($data)){
                    $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    if($continue == 'yes'){
                        $lastInsertId = $this->productTable->getLastInsertValue();
                        if($lastInsertId){
                            return $this->redirect()->toUrl('/product/detail/id/'.$lastInsertId);
                        }
                    }
                    return $this->redirect()->toUrl('/product');
                }else{
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                        $view->setVariable('form',$form);
                        return $view;
                    }else{
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['INSERT_FAIL']);
                        return $this->redirect()->toUrl('/product');
                    }
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->flashMessenger()->setNamespace('error')->addMessage($msg);
                }
                return $this->redirect()->toUrl('/product');
            }
        }
        $view->setVariable('form',$form);
        return $view;
    }
    public function editAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $form = new ProductForm($sm);
        $view = new ViewModel();
        $id = (int) $this->params('id',0);
        if(!$id || $id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        if(!$this->productTable){
            $this->productTable = $sm->get('ProductTable');
        }
        $entry = $this->productTable->getTdmProduct($id);
        if(empty($entry)){
            $this->getResponse()->setStatusCode(404);
        }
        $view->setVariable('model',$entry->name);
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['brand'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_BRAND_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['model'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!is_numeric($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_VALID']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/product');
                }
                if($this->save($data)){
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('success' => $messages['UPDATE_SUCCESS']));
                        $newEntry = $this->productTable->getTdmProduct($id);
                        $view->setVariable('model',$newEntry->name);
                        $newEntryArray = (array) $newEntry;
                        $form->setData($newEntryArray);
                        $view->setVariable('form',$form);
                        return $view;
                    }else{
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPDATE_SUCCESS']);
                        return $this->redirect()->toUrl('/product');
                    }
                }else{
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('danger' => $messages['UPDATE_FAIL']));
                        $view->setVariable('form',$form);
                        return $view;
                    }else{
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['UPDATE_FAIL']);
                        return $this->redirect()->toUrl('/product');
                    }
                }
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
        if(!$this->productTable){
            $this->productTable = $sm->get('ProductTable');
        }
        if(!$this->tdmProductTable){
            $this->tdmProductTable = $sm->get('TdmProductTable');
        }
        $dataFinal = $data;
        $product = new Product();
        $product->exchangeArray($dataFinal);
        $id = $product->product_id;
        $lastestInsertId = $id;
        if($id != 0){
            $result1 = $this->productTable->save($product);
        }else{
            if($this->productTable->save($product)){
                $result1 = true;
                $lastestInsertId = $this->productTable->getLastInsertValue();
            }else{
                $result1 = false;
            }
        }
        if(isset($lastestInsertId)){
            $dataFinal['product_id'] = $lastestInsertId;
            $tdmProduct = new TdmProduct();
            $tdmProduct->exchangeArray($dataFinal);
            $result2 = $this->tdmProductTable->save($tdmProduct);
        }
        return ($result1 || $result2) ? true : false;
    }
    public function addAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $form = new ProductForm($sm);
        $view = new ViewModel();
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['brand'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_BRAND_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['model'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_MODEL_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!is_numeric($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_VALID']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/product');
                }
                if($this->save($data)){
                    $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    if($continue == 'yes'){
                        $lastInsertId = $this->productTable->getLastInsertValue();
                        if($lastInsertId){
                            return $this->redirect()->toUrl('/product/edit/id/'.$lastInsertId);
                        }
                    }
                    return $this->redirect()->toUrl('/product');
                }else{
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                        $view->setVariable('form',$form);
                        return $view;
                    }else{
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['INSERT_FAIL']);
                        return $this->redirect()->toUrl('/product');
                    }
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->flashMessenger()->setNamespace('error')->addMessage($msg);
                }
                return $this->redirect()->toUrl('/product');
            }
        }
        $view->setVariable('form',$form);
        return $view;
    }
    public function detailAction()
    {
        $view = new ViewModel();
        return $view;
    }
    public function deleteAction()
    {
        $this->auth();
        $id = $this->params('id',0);
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        if(!$this->productTable){
            $this->productTable = $this->serviceLocator->get('ProductTable');
        }
        if($id != 0){
            $this->delete($id,$this->productTable);
        }
        if(!empty($ids) && is_array($ids)){
            foreach($ids as $id){
                $this->delete($id,$this->productTable);
            }
        }
        return $this->redirect()->toUrl('/product');
    }
    protected function delete($id,$table)
    {
        $messages = $this->getMessages();
        if($table->deleteEntry($id)){
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
        return ;
    }
    public function conditionAction()
    {

    }
    public function addConditionAction()
    {

    }
    public function exportAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $format = $this->params('format');
        if(!$format){
            return true;
        }
        $request = $this->getRequest();
        $ids = $request->getQuery('id');
        $viewhelperManager = $this->getServiceLocator()->get('viewhelpermanager');
        if (!$this->productTable) {
            $sm = $this->getServiceLocator();
            $this->productTable = $sm->get('ProductTable');
        }
        $rowset = $this->productTable->getAvaiableTdmProducts($ids);
        $header = array('Product ID','Brand','Model','Type','Country','Price','Currency','Name','Condition');
        $data = array($header);
        if(!empty($rowset)){
            foreach($rowset as $row){
                $rowParse = array();
                $rowParse[] = $row->product_id;
                $rowParse[] = $row->brand;
                $rowParse[] = $row->model;
                $rowParse[] = $viewhelperManager->get('Type')->implement($row->type_id);
                $rowParse[] = $viewhelperManager->get('Country')->implement($row->country_id);
                $rowParse[] = $row->price;
                $rowParse[] = $row->currency;
                $rowParse[] = $row->name;
                $rowParse[] = $viewhelperManager->get('Condition')->implement($row->condition_id);
                $data[] = $rowParse;
            }
        }
        if(!empty($data)){
            $filename = 'models_export_'.date('Y_m_d');
            if($format == 'csv'){
                $excel = new Csv();
                $excel->fromArray($data);
                $excel->download($filename.'.csv');
            }elseif($format == 'xlsx'){
                $parseExcelData = array($data);
                $excel = new Xlsx();
                $excel->fromArray($parseExcelData);
                $excel->download($filename.'.xlsx');
            }elseif($format == 'xls'){
                $parseExcelData = array($data);
                $excel = new Xls();
                $excel->fromArray($parseExcelData);
                $excel->download($filename.'.xls');
            }
        }else{
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['EXPORT_FAIL']);
            return $this->redirect()->toUrl('/product');
        }
        exit();
    }
}
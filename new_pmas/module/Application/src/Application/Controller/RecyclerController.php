<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/13/13
 */
namespace Application\Controller;

use Application\Form\RecyclerForm;
use Application\Form\RecyclerProductForm;
use Application\Form\TmpProductForm;
use BasicExcel\Reader;
use BasicExcel\Writer\Csv;
use BasicExcel\Writer\Xls;
use BasicExcel\Writer\Xlsx;
use Core\Controller\AbstractController;
use Core\Model\CreatePath;
use Core\Model\Recycler;
use Core\Model\RecyclerProduct;
use Core\Model\RecyclerProductTable;
use Core\Model\Referer;
use Core\Model\SlugFile;
use Core\Model\TdmProduct;
use Core\Model\TmpProduct;
use SimpleExcel\SimpleExcel;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\Debug\Debug;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;

class RecyclerController extends AbstractController
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
        parent::initAction();
        $request = $this->getRequest();
        $ppp = $request->getQuery('ppp');
        if(!empty($ppp)){
            $this->getViewHelperPlugin('core')->setItemPerPage($ppp);
        }
        $item_per_page = $this->getViewHelperPlugin('core')->getItemPerPage();
        $page = trim($this->params('page',1),'/');
        if (!$this->recyclerTable) {
            $sm = $this->getServiceLocator();
            $this->recyclerTable = $sm->get('RecyclerTable');
            $select = $this->recyclerTable->getRecyclerQuery();
            $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
            $paginator = new Paginator(new DbSelect($select,$dbAdapter));
            $paginator->setItemCountPerPage($item_per_page);
            $paginator->setCurrentPageNumber($page);
            $this->setViewVariable('paginator', $paginator);
        }
        return $this->view;
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
             * Check empty
             */
            if(!$empty->isValid($post['email'])){
                $view->setVariable('msg',array('danger' => $messages['RECYCLER_EMAIL_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            $emailAddress = new EmailAddress();
            if(!$emailAddress->isValid($post['email'])){
                $view->setVariable('msg',array('danger' => $messages['RECYCLER_EMAIL_INVALID']));
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/recycler');
                }
                if($this->save($data)){
                    $lastInsertId = $this->recyclerTable->getLastInsertValue();
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\add',$messages['LOG_INSERT_RECYCLER_SUCCESS'].$lastInsertId);
                    $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['INSERT_SUCCESS']));
                    if($continue == 'yes'){

                        if($lastInsertId){
                            return $this->redirect()->toUrl('/recycler/detail/id/'.$lastInsertId);
                        }
                    }
                    return $this->redirect()->toUrl('/recycler');
                }else{
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\add',$messages['LOG_INSERT_RECYCLER_FAIL']);
                    $view->setVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                    $view->setVariable('form',$form);
                    return $view;
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\add',$messages['LOG_INSERT_RECYCLER_FAIL']);
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
        parent::initAction();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $view = new ViewModel();
        $id = (int) $this->params('id',0);
        if(!$id || $id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        $view->setVariable('id',$id);
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/recycler');
                }
                if($this->save($data)){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\detail',$messages['LOG_UPDATE_RECYCLER_SUCCESS'].$id);
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('success' => $messages['UPDATE_SUCCESS']));
                        $view->setVariable('form',$form);
                        return $view;
                    }else{
                        $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['UPDATE_SUCCESS']));
                        return $this->redirect()->toUrl('/recycler');
                    }
                }else{
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\detail',$messages['LOG_UPDATE_RECYCLER_FAIL'].$id);
                    $view->setVariable('msg',array('danger' => $messages['UPDATE_FAIL']));
                    $view->setVariable('form',$form);
                    return $view;
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\detail',$messages['LOG_UPDATE_RECYCLER_FAIL'].$id);
                    $view->setVariable('msg',array('danger' => $msg));
                }
                $view->setVariable('form',$form);
                return $view;
            }
        }else{
            $ppp = $request->getQuery('ppp');
            $params = $request->getQuery();
            if(!empty($ppp)){
                $this->getViewHelperPlugin('core')->setItemPerPage($ppp);
            }
            $item_per_page = $this->getViewHelperPlugin('core')->getItemPerPage();
            $page = trim($this->params('page',null),'/');
            $currentPage = ($page != null) ? $page : 1;
            $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
            $entryArray = (array) $entry;
            $form->setData($entryArray);
            $tmpProductTable = $this->getServiceLocator()->get('TmpProductTable');
            $reyclerProductTable = $this->getServiceLocator()->get('RecyclerProductTable');
            $productsInRecyclerQuery = $reyclerProductTable->getProductsByRecyclerQuery($id,$params);
            $upload = $this->params('upload');
            if(!$upload){
                $tmpProductTable->deleteByRecyclerId($id);
                $productsInRecycler = new Paginator(new DbSelect($productsInRecyclerQuery,$dbAdapter));
                $productsInRecycler->setItemCountPerPage($item_per_page);
                $productsInRecycler->setCurrentPageNumber($currentPage);
                $view->setVariable('products',$productsInRecycler);
            }else{
                $productsInRecyclerQuery = $tmpProductTable->getRowsByRecyclerIdQuery($id);
                $productsInRecycler = new Paginator(new DbSelect($productsInRecyclerQuery,$dbAdapter));
                $productsInRecycler->setItemCountPerPage($item_per_page);
                $productsInRecycler->setCurrentPageNumber($currentPage);
                $view->setVariable('tmpProducts',$productsInRecycler);
            }
            $from = $request->getQuery('from');
            $view->setVariable('upload',$upload);
            $view->setVariable('from',$from);
            $view->setVariable('page',$page);
            $view->setVariable('ppp',$ppp);
            $view->setVariable('params',$params);
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
    public function deleteAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $id = $this->params('id',0);
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        if(!$this->recyclerTable){
            $this->recyclerTable = $this->serviceLocator->get('RecyclerTable');
        }
        if($id != 0){
            if($this->delete($id,$this->recyclerTable)){
                $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['DELETE_SUCCESS']));
            }else{
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['DELETE_FAIL']));
            }
        }else{
            if(!empty($ids) && is_array($ids)){
                $i= 0 ;
                foreach($ids as $id){
                    if($this->delete($id,$this->recyclerTable)){
                        $i++;
                    }
                }
                $this->flashMessenger()->setNamespace('success')->addMessage($i.$this->__($messages['QTY_RECYCLERS_DELETE_SUCCESS']));
            }
        }
        return $this->redirect()->toUrl('/recycler');
    }
    protected function delete($id,$table)
    {
        $messages = $this->getMessages();
        if($table->clearRecycler($id)){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\delete',$messages['LOG_DELETE_RECYCLER_SUCCESS'].$id);
            return true;
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\delete',$messages['LOG_DELETE_RECYCLER_FAIL'].$id);
            return false;
        }
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
        if(!$this->recyclerTable){
            $this->recyclerTable = $this->serviceLocator->get('RecyclerTable');
        }
        $rowset = $this->recyclerTable->getAvailabeRecyclers($ids);
        $header = array($this->__('Recycler Id'),
            $this->__('Name'),
            $this->__('Country'),
            $this->__('Email'),
            $this->__('Website'),
            $this->__('Telephone'),
            $this->__('Address'));
        $data = array($header);
        if(!empty($rowset)){
            foreach($rowset as $row){
                $rowParse = array();
                $rowParse[] = $row->recycler_id;
                $rowParse[] = $row->name;
                $rowParse[] = $viewhelperManager->get('Country')->implement($row->country_id);
                $rowParse[] = $row->email;
                $rowParse[] = $row->website;
                $rowParse[] = $row->telephone;
                $rowParse[] = $row->address;
                $data[] = $rowParse;
            }
        }
        if(!empty($data)){
            $filename = 'recyclers_export_'.date('Y_m_d');
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
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\export',$messages['LOG_EXPORT_RECYCLERS_SUCCESS']);
        }
        exit();
        die;
    }
    public function importAction()
    {
        parent::initAction();
        $request = $this->getRequest();
        $messages = $this->getMessages();
        $viewhelperManager = $this->getServiceLocator()->get('viewhelpermanager');
        if($request->isPost()){
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $path = getcwd() . "/upload/import";
            CreatePath::createPath($path);
            /*if (!is_dir($path)) {
                if (!@mkdir($path, 0777, true)) {
                    throw new \Exception("Unable to create destination: " . $path);
                }
            }*/
            if(empty($post)){
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                return $this->redirect()->toUrl('/recycler');
            }
            $recycler_id = (int) $post['recycler_id'];
            if($post['upload_file']['name'] && trim($post['upload_file']['name']) != ''){
                $ext = pathinfo($post['upload_file']['name'], PATHINFO_EXTENSION);
                if (!file_exists($path .DIRECTORY_SEPARATOR . $post['upload_file']['name'])) {
                    move_uploaded_file($post['upload_file']['tmp_name'], $path .DIRECTORY_SEPARATOR .$post['upload_file']['name'] );
                }
                $dataImport = array();
                if(strtolower($ext) == 'xlsx'){
                    $file = new Reader\Xlsx();
                    $file->load($path .DIRECTORY_SEPARATOR .$post['upload_file']['name']);
                    $dataImport = $file->toArray();
                }elseif(strtolower($ext) == 'xls'){
                    $file = new Reader\Xls();
                    $file->read($path .DIRECTORY_SEPARATOR .$post['upload_file']['name']);
                    $dataImport = $file->toArray();
                }else{
                    $file = new Reader\Csv();
                    $file->load($path .DIRECTORY_SEPARATOR .$post['upload_file']['name']);
                    $dataImport = $file->toArray();
                }
                $dataParse = array();
                $tmpProduct = new TmpProduct();
                $tmpProductTable = $this->getServiceLocator()->get('TmpProductTable');
                if(empty($dataImport)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/recycler/detail/id/'.$recycler_id);
                }
                $header = array();
                $first = $dataImport[0];
                foreach($first as $index=>$value){
                    $header[$index] = $viewhelperManager->get('slugify')->implement($value);
                }
                /**
                 * If header null, return
                 */
                if(empty($header)){
                    exit();
                }
                $brandTable = $this->sm->get('BrandTable');
                foreach($dataImport as $i=>$row){
                    if($i>0){
                        $rowParse = array();
                        $rowParse['recycler_id'] = $recycler_id;
                        $rowParse['brand_id'] = $brandTable->getBrandIdByName(trim($row[array_search('brand',$header)]));
                        $rowParse['model'] = $row[array_search('model',$header)];
                        $rowParse['type_id'] = ($viewhelperManager->get('product_type')->getTypeIdByName(trim($row[array_search('product-type',$header)])) != null) ? $viewhelperManager->get('product_type')->getTypeIdByName(trim($row[array_search('product-type',$header)])) : 0;
                        $rowParse['date'] = $row[array_search('date',$header)];
                        $rowParse['price'] = $row[array_search('price',$header)];
                        $rowParse['currency'] = $row[array_search('currency',$header)];
                        $rowParse['name'] = $row[array_search('name',$header)];
                        $rowParse['condition_id'] = ($viewhelperManager->get('Condition')->getRecyclerConditionIdByName(trim($row[array_search('condition',$header)])) != null) ? $viewhelperManager->get('Condition')->getRecyclerConditionIdByName(trim($row[array_search('condition',$header)])) : 0;
                        $tmpProduct->exchangeArray($rowParse);
                        $tmpProductTable->save($tmpProduct);
                    }
                }
                /**
                 * Delete upload file
                 */
                @unlink($path .DIRECTORY_SEPARATOR .$post['upload_file']['name']);
                $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\import',$messages['LOG_IMPORT_RECYCLERS_SUCCESS'].$recycler_id);
                $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['UPLOAD_SUCCESS']));
            }else{
                $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\import',$messages['LOG_IMPORT_RECYCLERS_FAIL'].$recycler_id);
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                return $this->redirect()->toUrl('/recycler/detail/id/'.$recycler_id);
            }
            return $this->redirect()->toUrl('/recycler/detail/id/'.$recycler_id.'/upload/success');
        }else{
            return $this->redirect()->toUrl('/recycler');
        }
        exit();
    }
    public function saveImportAction()
    {
        $id = $this->params('id');
        if(!$id){
            echo 'Record does not exist.';
            return true;
        }
        $tmpProductTable = $this->getServiceLocator()->get('TmpProductTable');
        $recyclerProductTable = $this->getServiceLocator()->get('RecyclerProductTable');
        $tmpProductEntry = $tmpProductTable->getEntry($id);
        if(!empty($tmpProductEntry)){
            if(!$recyclerProductTable->hasTempId($id)){
                $tmpEntryParse = (array) $tmpProductEntry;
                $tmpEntryParse['temp_id'] = $id;
                $pDate = \DateTime::createFromFormat('d-m-Y H:i:s',$tmpProductEntry->date.' 00:00:00');
                if($pDate){
                    $tmpEntryParse['date'] = $pDate->getTimestamp();
                }else{
                    $tmpEntryParse['date'] = 0;
                }
                $recyclerProduct = new RecyclerProduct();
                $recyclerProduct->exchangeArray($tmpEntryParse);
                if($recyclerProductTable->save($recyclerProduct)){
                    echo $this->__('Save record success.');
                }else{
                    echo $this->__('Save record not success.');
                }
            }else{
                echo $this->__('This product has already saved.');
            }
        }else{
            echo $this->__('No data existed.');
        }
        die;
    }
    public function saveImportAllAction()
    {
        parent::initAction();
        $recycler = $this->params('recycler',0);
        if(!$recycler || $recycler == 0){
            echo 'Record does not exist.';
            return false;
        }
        $tmpProductTable = $this->sm->get('TmpProductTable');
        $recyclerProductTable = $this->sm->get('RecyclerProductTable');
        $rowset = $tmpProductTable->getRowsByRecyclerId($recycler);
        if(!empty($rowset)){
            foreach($rowset as $row){
                if(!$recyclerProductTable->hasTempId($row->id)){
                    $tmpEntryParse = (array) $row;
                    $tmpEntryParse['temp_id'] = $row->id;
                    $pDate = \DateTime::createFromFormat('d-m-Y H:i:s',$row->date.' 00:00:00');
                    if($pDate){
                        $tmpEntryParse['date'] = $pDate->getTimestamp();
                    }else{
                        $tmpEntryParse['date'] = 0;
                    }
                    $recyclerProductTable->saveData($tmpEntryParse);
                }
            }
        }
        $this->addSuccessFlashMessenger($this->__('Save all success.'));
        $this->redirectUrl('/recycler/detail/id/'.$recycler);
    }
    public function productAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $view = new ViewModel();
        $id = $this->params('id',0);
        if($id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        $form = new RecyclerProductForm($this->getServiceLocator());
        $recyclerProductTable = $this->getServiceLocator()->get('RecyclerProductTable');
        $entry = $recyclerProductTable->getEntry($id);
        if(empty($entry)){
            $this->getResponse()->setStatusCode(404);
        }
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
            if(!$empty->isValid($post['model'])){
                $view->setVariable('msg',array('danger' => $messages['MODEL_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            $select_empty = new NotEmpty(array('integer','zero'));
            if(!$select_empty->isValid($post['brand_id'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_BRAND_NOT_SELECTED']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$select_empty->isValid($post['type_id'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_TYPE_NOT_SELECTED']));
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
                $data['temp_id'] = $entry->temp_id;
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/recycler/detail/id/'.$entry->recycler_id);
                }
                if($this->saveRecyclerProduct($data)){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\product',$messages['LOG_UPDATE_RECYCLER_PRODUCT_SUCCESS'].$id);
                    if($continue == 'yes'){
                        $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['UPDATE_SUCCESS']));
                        return $this->redirect()->toUrl('/recycler/product/id/'.$id);
                    }else{
                        $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['UPDATE_SUCCESS']));
                        return $this->redirect()->toUrl('/recycler/detail/id/'.$entry->recycler_id);
                    }
                }else{
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\product',$messages['LOG_UPDATE_RECYCLER_PRODUCT_FAIL'].$id);
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['UPDATE_FAIL']));
                    return $this->redirect()->toUrl('/recycler/product/id/'.$id);
                }
            }
        }else{
            $formData = (array) $entry;
            if(!empty($entry->date)){
                $formData['date'] = date('d-m-Y',$entry->date);
            }else{
                $formData['date'] = '';
            }
            $form->setData($formData);
            $view->setVariable('recycler_id',$entry->recycler_id);
        }
        $view->setVariable('model',$entry->name);
        $view->setVariable('form',$form);
        return $view;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function saveRecyclerProduct($data)
    {
        $sm = $this->getServiceLocator();
        $recyclerProductTable = $sm->get('RecyclerProductTable');
        $dataFinal = $data;
        $date = \DateTime::createFromFormat('d-m-Y',$data['date']);
        if($date){
            $dataFinal['date'] = $date->getTimestamp();
        }
        $recyclerProduct = new RecyclerProduct();
        $recyclerProduct->exchangeArray($dataFinal);
        return $recyclerProductTable->save($recyclerProduct);
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function saveTemp($data)
    {
        $sm = $this->getServiceLocator();
        $tmpProductTable = $sm->get('TmpProductTable');
        $dataFinal = $data;
        $tmpProduct = new TmpProduct();
        $tmpProduct->exchangeArray($dataFinal);
        return $tmpProductTable->save($tmpProduct);
    }
    public function tempAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $view = new ViewModel();
        $id = $this->params('id',0);
        if($id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        $form = new TmpProductForm($this->getServiceLocator());
        $view->setVariable('form',$form);
        $tmpProductTable = $this->getServiceLocator()->get('TmpProductTable');
        $entry = $tmpProductTable->getEntry($id);
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
            if(!$empty->isValid($post['model'])){
                $view->setVariable('msg',array('danger' => $messages['MODEL_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            $select_empty = new NotEmpty(array('integer','zero'));
            if(!$select_empty->isValid($post['brand_id'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_BRAND_NOT_SELECTED']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$select_empty->isValid($post['type_id'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_TYPE_NOT_SELECTED']));
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/recycler/temp');
                }
                if($this->saveTemp($data)){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\temp',$messages['LOG_UPDATE_TEMP_RECYCLER_PRODUCT_SUCCESS'].$id);
                    if($continue == 'yes'){
                        $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['UPDATE_SUCCESS']));
                        return $this->redirect()->toUrl('/recycler/temp/id/'.$id);
                    }else{
                        $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['UPDATE_SUCCESS']));
                        return $this->redirect()->toUrl('/recycler/temp');
                    }
                }else{
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\temp',$messages['LOG_UPDATE_TEMP_RECYCLER_PRODUCT_FAIL'].$id);
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['UPDATE_FAIL']));
                    return $this->redirect()->toUrl('/recycler/temp/id/'.$id);
                }
            }
        }else{
            $formData = (array) $entry;
            $form->setData($formData);
        }
        return $view;
    }
    public function exportRecyclerProductsAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $recycler_id = $this->params('id',0);
        $format = $this->params('format');
        if(!$format){
            $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['FORMAT_EXPORT_INVALID']));
            return $this->redirect()->toUrl('/recycler/detail/id'.$recycler_id);
        }
        if($recycler_id == 0){
            $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['EXPORT_FAIL']));
            return $this->redirect()->toUrl('/recycler');
        }
        $viewhelperManager = $this->getServiceLocator()->get('viewhelpermanager');
        $priceHelper = $viewhelperManager->get('Price');
        $recyclerHelper = $viewhelperManager->get('Recycler');
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        $recyclerProducts = $recyclerProductTable->getProductsByRecycler($recycler_id);
        $header = array($this->__('Recycler ID'),
            $this->__('Brand'),
            $this->__('Model'),
            $this->__('Product type'),
            $this->__('Price'),
            $this->__('Currency'),
            $this->__('Name'),
            $this->__('Date'),
            $this->__('Condition'));
        $data = array($header);
        if(!empty($recyclerProducts)){
            foreach($recyclerProducts as $row){
                $rowParse = array();
                $rowParse[] = $row->recycler_id;
                $rowParse[] = $viewhelperManager->get('product_brand')->getName($row->brand_id);
                $rowParse[] = $row->model;
                $rowParse[] = $viewhelperManager->get('product_type')->getName($row->type_id);
                $rowParse[] = $priceHelper->format($row->price);
                $rowParse[] = $row->currency;
                $rowParse[] = $row->name;
                $rowParse[] = (!empty($row->date)) ? date('d-m-Y H:i:s',$row->date) : 'N/A';
                $rowParse[] = $viewhelperManager->get('Condition')->implement($row->condition_id,false);
                $data[] = $rowParse;
            }
        }
        if(!empty($data)){
            $parseRecyclerName = SlugFile::parseFilename($recyclerHelper->getName($recycler_id));
            $filename = $parseRecyclerName.'_products_export_'.date('Y_m_d');
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
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\temp',$messages['LOG_EXPORT_RECYCLER_PRODUCT_SUCCESS'].$recycler_id);
        }else{
            $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['EXPORT_FAIL']));
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\temp',$messages['LOG_EXPORT_RECYCLER_PRODUCT_FAIL'].$recycler_id);
            return $this->redirect()->toUrl('/recycler/detail/id/'.$recycler_id);
        }
        exit();
    }
    public function deleteProductAction()
    {
        parent::initAction();
        $messages = $this->getMessages();
        $id = $this->params('id',0);
        $recycler = $this->params('recycler',0);
        $request = $this->getRequest();
        $ids = $request->getQuery('id');
        if(!$id || $id == 0 || !$recycler || $recycler == 0){
            $this->getResponse()->setStatusCode(404);
        }
        $recyclerProductTable = $this->sm->get('RecyclerProductTable');
        if($id != 0){
            if($this->delete_product($id,$recyclerProductTable)){
                $this->addSuccessFlashMessenger($this->__($messages['DELETE_RECYCLER_PRODUCT_SUCCESS']).$id);
            }else{
                $this->addSuccessFlashMessenger($this->__($messages['DELETE_RECYCLER_PRODUCT_FAIL']).$id);
            }
        }
        if(!empty($ids) && is_array($ids)){
            $i = 0;
            foreach($ids as $id){
                if($this->delete_product($id,$recyclerProductTable)){
                    $i++;
                }
            }
            $this->addSuccessFlashMessenger($i.$messages['QTY_RECYCLER_PRODUCTS_DELETE_SUCCESS']);
        }
        return $this->redirectUrl('/recycler/detail/id/'.$recycler.'?from=delete');
    }

    /**
     * @param $id
     * @param $table
     * @return bool
     */
    protected function delete_product($id,$table)
    {
        $messages = $this->getMessages();
        if($table->delete($id)){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\delete-product',$messages['LOG_DELETE_RECYCLER_PRODUCT_SUCCESS'].$id);
            return true;
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\recycler\\delete-product',$messages['LOG_DELETE_RECYCLER_PRODUCT_FAIL'].$id);
            return false;
        }
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/13/13
 */
namespace Application\Controller;

use Application\Form\RecyclerForm;
use BasicExcel\Reader;
use BasicExcel\Writer\Csv;
use BasicExcel\Writer\Xls;
use BasicExcel\Writer\Xlsx;
use Core\Model\CreatePath;
use Core\Model\Product;
use Core\Model\Recycler;
use Core\Model\RecyclerProduct;
use Core\Model\RecyclerProductTable;
use Core\Model\TdmProduct;
use Core\Model\TmpProduct;
use SimpleExcel\SimpleExcel;
use Zend\Debug\Debug;
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
            }else{
                foreach($form->getMessages() as $msg){
                    $this->flashMessenger()->setNamespace('error')->addMessage($msg);
                }
                return $this->redirect()->toUrl('/recycler');
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
            $tmpProductTable = $this->getServiceLocator()->get('TmpProductTable');
            $tmpProducts = $tmpProductTable->getRowsByRecyclerId($id);
            $view->setVariable('tmpProducts',$tmpProducts);
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
        $id = $this->params('id',0);
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        if(!$this->recyclerTable){
            $this->recyclerTable = $this->serviceLocator->get('RecyclerTable');
        }
        if($id != 0){
            $this->delete($id,$this->recyclerTable);
        }else{
            if(!empty($ids) && is_array($ids)){
                foreach($ids as $id){
                    $this->delete($id,$this->recyclerTable);
                }
            }
        }
        return $this->redirect()->toUrl('/recycler');
    }
    protected function delete($id,$table)
    {
        $messages = $this->getMessages();
        if($table->clearRecycler($id)){
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
        return true;
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
        $header = array('Recycler Id','Name','Country','Email','Website','Telephone','Address');
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
        }else{
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['EXPORT_FAIL']);
            return $this->redirect()->toUrl('/recycler');
        }
        exit();
        die;
    }
    public function importAction()
    {
        $this->auth();
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
                $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/recycler');
                }
                foreach($dataImport as $i=>$row){
                    if($i>0){
                        $rowParse = array();
                        $rowParse['recycler_id'] = $recycler_id;
                        $rowParse['brand_id'] = $viewhelperManager->get('ProductBrand')->getBrandIdByName(trim($row[0]));
                        $rowParse['model'] = $row[1];
                        $rowParse['type_id'] = $viewhelperManager->get('ProductType')->getTypeIdByName(trim($row[2]));
                        $rowParse['country_id'] = $viewhelperManager->get('Country')->getCountryNameById(trim($row[3]));
                        $rowParse['price'] = $row[4];
                        $rowParse['currency'] = $row[5];
                        $rowParse['name'] = $row[6];
                        $rowParse['condition_id'] = $viewhelperManager->get('Condition')->getRecyclerConditionIdByName(trim($row[7]));
                        $tmpProduct->exchangeArray($rowParse);
                        $tmpProductTable->save($tmpProduct);
                    }
                }
                /**
                 * Delete upload file
                 */
                @unlink($path .DIRECTORY_SEPARATOR .$post['upload_file']['name']);
                $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPLOAD_SUCCESS']);
            }else{
                $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                return $this->redirect()->toUrl('/recycler');
            }
            return $this->redirect()->toUrl('/recycler/detail/id/'.$recycler_id);
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
            $tmpEntryParse = (array) $tmpProductEntry;
            $tmpEntryParse['temp_id'] = $id;
            $recyclerProduct = new RecyclerProduct();
            $recyclerProduct->exchangeArray($tmpEntryParse);
            if($recyclerProductTable->save($recyclerProduct)){
                echo 'Save record success.';
            }else{
                echo 'Save record not success.';
            }
        }else{
            echo 'No data existed.';
        }
        die;
    }
}
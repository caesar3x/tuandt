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
use Core\Model\Device;
use Core\Model\Recycler;
use Core\Model\RecyclerDevice;
use Core\Model\TmpDevice;
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
            $tmpDeviceTable = $this->getServiceLocator()->get('TmpDeviceTable');
            $tmpDevices = $tmpDeviceTable->getRowsByRecyclerId($id);
            $view->setVariable('tmpDevices',$tmpDevices);
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
        if($table->deleteEntry($id)){
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
        if($request->isPost()){
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $path = getcwd() . "/upload/import";
            if (!is_dir($path)) {
                if (!@mkdir($path, 0777, true)) {
                    throw new \Exception("Unable to create destination: " . $path);
                }
            }
            if(empty($post)){
                $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                return $this->redirect()->toUrl('/recycler');
            }
            $recycler_id = (int) $post['recycler_id'];
            if($post['upload_file']['name'] && trim($post['upload_file']['name']) != ''){
                if (!file_exists($path .DIRECTORY_SEPARATOR . $post['upload_file']['name'])) {
                    move_uploaded_file($post['upload_file']['tmp_name'], $path .DIRECTORY_SEPARATOR .$post['upload_file']['name'] );
                }
            }
            $file = new Reader\Csv();
            $file->load($path .DIRECTORY_SEPARATOR .$post['upload_file']['name']);
            $dataImport = $file->toArray();
            $dataParse = array();
            $tmpDevice = new TmpDevice();
            $tmpDeviceTable = $this->getServiceLocator()->get('TmpDeviceTable');
            foreach($dataImport as $i=>$row){
                if($i>0){
                    $rowParse = array();
                    $rowParse['recycler_id'] = $recycler_id;
                    $rowParse['brand'] = $row[0];
                    $rowParse['model'] = $row[1];
                    $rowParse['type_id'] = $row[2];
                    $rowParse['country_id'] = $row[3];
                    $rowParse['price'] = $row[4];
                    $rowParse['currency'] = $row[5];
                    $rowParse['name'] = $row[6];
                    $rowParse['condition_id'] = $row[7];
                    $tmpDevice->exchangeArray($rowParse);
                    $tmpDeviceTable->save($tmpDevice);
                }
            }
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPLOAD_SUCCESS']);
            return $this->redirect()->toUrl('/recycler/detail/id/'.$recycler_id);
        }else{
            return $this->redirect()->toUrl('/recycler');
        }
        exit();
        /*$excel = new SimpleExcel('CSV');
        $excel->parser->loadFile($_FILES['upload_file']);
        Debug::dump($excel->parser->getRow(1)) ;
        die('importAction');*/
    }
    public function saveImportAction()
    {
        $id = $this->params('id');
        if(!$id){
            echo 'Record does not exist.';
            return true;
        }
        $tmpDeviceTable = $this->getServiceLocator()->get('TmpDeviceTable');
        $deviceTable = $this->getServiceLocator()->get('DeviceTable');
        $recyclerDeviceTable = $this->getServiceLocator()->get('RecyclerDeviceTable');
        $tmpDeviceEntry = $tmpDeviceTable->getEntry($id);
        if(!empty($tmpDeviceEntry)){
            $tmpEntryParse = (array) $tmpDeviceEntry;

            $device = new Device();
            $device->exchangeArray($tmpEntryParse);
            if($deviceTable->save($device)){
                $device_id = $deviceTable->getLastInsertValue();
                $tmpEntryParse['device_id'] = $device_id;
            }
            $recyclerDevice = new RecyclerDevice();
            $recyclerDevice->exchangeArray($tmpEntryParse);
            if($recyclerDeviceTable->save($recyclerDevice)){
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
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/13/13
 */
namespace Application\Controller;

use Application\Form\BrandForm;
use Application\Form\ProductForm;
use Application\Form\ProductTypeForm;
use BasicExcel\Writer\Csv;
use BasicExcel\Writer\Xls;
use BasicExcel\Writer\Xlsx;
use Core\Controller\AbstractController;
use Core\Model\Brand;
use Core\Model\CacheSerializer;
use Core\Model\ProductType;
use Core\Model\SlugFile;
use Core\Model\TdmProduct;
use Zend\Debug\Debug;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\Digits;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;

class ProductController extends AbstractController
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
        $tdmProductTable = $this->getServiceLocator()->get('TdmProductTable');
        $rowset = $tdmProductTable->getAvaiableRows();
        $view->setVariable('rowset',$rowset);
        $country = $this->params('country',null);
        $this->setViewVariable('country',$country);
        return $view;
    }
    public function filterAction()
    {
        $this->auth();
        $view = new ViewModel();
        $higher = $this->params('higher',null);
        $country = $this->params('country',null);
        $view->setVariable('higher',$higher);
        $view->setVariable('country',$country);
        $messages = $this->getMessages();
        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\filter',$messages['LOG_VIEW_PRODUCT_FILTER']);
        $recyclerProductTable = $this->getServiceLocator()->get('RecyclerProductTable');
        if($higher != null){
            $rowset = $recyclerProductTable->getAvaiableRows();
        }elseif($country != null){
            $rowset = $recyclerProductTable->getProductsByCountry($country);
            /*foreach($rowset as $row){
                Debug::dump($row);
            }*/
        }
        $view->setVariable('rowset',$rowset);
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
        $tdmProductTable = $sm->get('TdmProductTable');
        $entry = $tdmProductTable->getEntry($id);
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
            if(!$select_empty->isValid($post['country_id'])){
                $view->setVariable('msg',array('danger' => $messages['COUNTRY_NOT_SELECTED']));
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/product');
                }
                if($this->saveTdmProduct($data)){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\edit',$messages['LOG_UPDATE_TDM_PRODUCT_SUCCESS'].$id);
                    if($continue == 'yes'){
                        $view->setVariable('msg',array('success' => $messages['UPDATE_SUCCESS']));
                        $newEntry = $tdmProductTable->getEntry($id);
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
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\edit',$messages['LOG_UPDATE_TDM_PRODUCT_FAIL'].$id);
                    $view->setVariable('msg',array('danger' => $messages['UPDATE_FAIL']));
                    $view->setVariable('form',$form);
                    return $view;
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\edit',$messages['LOG_UPDATE_TDM_PRODUCT_FAIL'].$id);
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
    public function saveTdmProduct($data)
    {
        $sm = $this->getServiceLocator();
        $tdmProductTable = $sm->get('TdmProductTable');
        $dataFinal = $data;
        $tdmProduct = new TdmProduct();
        $tdmProduct->exchangeArray($dataFinal);
        return $tdmProductTable->save($tdmProduct);
        /*$sm = $this->getServiceLocator();
        $tdmProductTable = $sm->get('TdmProductTable');
        $dataFinal = $data;
        $tdmProduct = new TdmProduct();
        $tdmProduct->exchangeArray($dataFinal);
        $id = $tdmProduct->product_id;
        $lastestInsertId = $id;
        if($id != 0){
            $result1 = $tdmProductTable->save($tdmProduct);
        }else{
            if($tdmProductTable->save($tdmProduct)){
                $result1 = true;
                $lastestInsertId = $tdmProductTable->getLastInsertValue();
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
        return ($result1 || $result2) ? true : false;*/
    }
    public function addAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $form = new ProductForm($sm);
        $view = new ViewModel();
        $tdmProductTable = $sm->get('TdmProductTable');
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
            if(!$select_empty->isValid($post['country_id'])){
                $view->setVariable('msg',array('danger' => $messages['COUNTRY_NOT_SELECTED']));
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/product');
                }
                if($this->saveTdmProduct($data)){
                    $lastInsertId = $tdmProductTable->getLastInsertValue();
                    $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\add',$messages['LOG_INSERT_TDM_PRODUCT_SUCCESS'].$lastInsertId);
                    if($continue == 'yes'){
                        if($lastInsertId){
                            return $this->redirect()->toUrl('/product/edit/id/'.$lastInsertId);
                        }
                    }
                    return $this->redirect()->toUrl('/product');
                }else{
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\add',$messages['LOG_INSERT_TDM_PRODUCT_FAIL']);
                    $view->setVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                    $view->setVariable('form',$form);
                    return $view;
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $view->setVariable('msg',array('danger' => $msg));
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\add',$messages['LOG_INSERT_TDM_PRODUCT_FAIL']);
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
        $id = $this->params('id',0);
        if($id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        $filter = $this->params('filter',null);
        $country = $this->params('country',null);

        $tdmProductTable = $this->getServiceLocator()->get('TdmProductTable');
        $entry = $tdmProductTable->getEntry($id);
        if(empty($entry)){
            $this->getResponse()->setStatusCode(404);
        }
        $view = new ViewModel();
        $view->setVariable('name',$entry->name);
        $view->setVariable('id',$id);
        $view->setVariable('entry',$entry);
        $view->setVariable('country',$country);
        $recyclerProductTable = $this->getServiceLocator()->get('RecyclerProductTable');
        $recyclerProductsWithSameModel = $recyclerProductTable->getRowsByModel($entry->model,$entry->condition_id);
        $messages = $this->getMessages();
        $exchangeHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('exchange');
        $tdmCurrentExchange = $exchangeHelper->getCurrentExchangeOfCurrency($entry->currency);
        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\detail',$messages['LOG_VIEW_TDM_PRODUCT'].$id);
        if(is_numeric($filter)){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\detail',$messages['LOG_FILTER_HIGHER_TDM_PRODUCT'].$id);
            $price = (float) $recyclerProductTable->getSSAPrice($entry->model,$entry->condition_id);
            if(!empty($price)){
                $tdmPriceExchange = $price*$tdmCurrentExchange;
                /**
                 * Filter higher than 50%
                 */
                if(!empty($recyclerProductsWithSameModel)){
                    $products = array();
                    foreach($recyclerProductsWithSameModel as $product){
                        $currentExchange = $exchangeHelper->getCurrentExchangeOfCurrency($product->currency);
                        $priceExchange = (float) $product->price * $currentExchange;
                        if($tdmPriceExchange != 0){
                            $percentage = (($priceExchange-$tdmPriceExchange)/$tdmPriceExchange)*100;
                            if($percentage > $filter){
                                $products[] = $product;
                            }
                        }
                    }
                    $view->setVariable('filter',$filter);
                    $view->setVariable('products',$products);
                    return $view;
                }
            }

        }
        if($country != null){
            $rowset = $this->getViewHelperPlugin('product')->getProductsByCountryAndModelAndCondition($country,$entry->model,$entry->condition_id);
            $view->setVariable('products',$rowset);
            return $view;
        }
        $view->setVariable('products',$recyclerProductsWithSameModel);
        return $view;
    }
    public function deleteAction()
    {
        $this->auth();
        $id = $this->params('id',0);
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        if($id != 0){
            $this->delete($id,$tdmProductTable);
        }
        if(!empty($ids) && is_array($ids)){
            foreach($ids as $id){
                $this->delete($id,$tdmProductTable);
            }
        }
        return $this->redirect()->toUrl('/product');
    }
    protected function delete($id,$table)
    {
        $messages = $this->getMessages();
        if($table->deleteEntry($id)){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete',$messages['LOG_DELETE_TDM_PRODUCT_SUCCESS'].$id);
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete',$messages['LOG_DELETE_TDM_PRODUCT_FAILT'].$id);
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
        return ;
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
        $sm = $this->getServiceLocator();
        if (!$this->productTable) {

            $this->productTable = $sm->get('TdmProductTable');
        }
        $reyclerProductTable = $sm->get('RecyclerProductTable');
        $rowset = $this->productTable->getProductsFilter($ids);
        $header = array('Product ID','Brand','Model','Product type','Country','SSA Price','Currency','Name','Condition');
        $data = array($header);
        if(!empty($rowset)){
            foreach($rowset as $row){
                $rowParse = array();
                $rowParse[] = $row->product_id;
                $rowParse[] = $viewhelperManager->get('ProductBrand')->implement($row->brand_id);
                $rowParse[] = $row->model;
                $rowParse[] = $viewhelperManager->get('ProductType')->implement($row->type_id);
                $rowParse[] = $viewhelperManager->get('Country')->implement($row->country_id);
                $rowParse[] = $reyclerProductTable->getSSAPrice($row->model,$row->condition_id);
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
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete',$messages['LOG_EXPORT_TDM_PRODUCTS_SUCCESS']);
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete',$messages['LOG_EXPORT_TDM_PRODUCTS_FAIL']);
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['EXPORT_FAIL']);
            return $this->redirect()->toUrl('/product');
        }
        exit();
    }
    public function typeAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $productTypeTable = $this->getServiceLocator()->get('ProductTypeTable');
        $types = $productTypeTable->getAvaiableRows();
        $view = new ViewModel();
        $form = new ProductTypeForm($this->getServiceLocator());
        $view->setVariable('form',$form);
        $view->setVariable('types',$types);
        $id = $this->params('id',0);
        if($id != 0){
            $typeEntry = $productTypeTable->getEntry($id);
            if(empty($typeEntry)){
                $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                return $this->redirect()->toUrl('/product/type');
            }
            $view->setVariable('name',$typeEntry->name);
            $entryParse = (array) $typeEntry;
            $form->setData($entryParse);
        }
        $view->setVariable('id',$id);
        $do = $this->params('do',null);
        $view->setVariable('do',$do);
        if($do != 'add' && $do != null){
            $this->redirect()->toUrl('/product/type');
        }
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['TYPE_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check condition exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            if($id != 0){
                $exist_valid = new NoRecordExists(array('table' => 'product_type','field' => 'name','adapter' => $dbAdapter,'exclude' => array('field' => 'name','value' => $typeEntry->name)));
            }else{
                $exist_valid = new NoRecordExists(array('table' => 'product_type','field' => 'name','adapter' => $dbAdapter));
            }
            if(!$exist_valid->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['TYPE_NAME_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/product/type');
                }
                if($this->saveProductType($data)){
                    if($id != 0){
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\type',$messages['LOG_UPDATE_PRODUCT_TYPE_SUCCESS'].$id);
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPDATE_SUCCESS']);
                    }else{
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\type',$messages['LOG_INSERT_PRODUCT_TYPE_SUCCESS'].$productTypeTable->getLastInsertValue());
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    }
                }else{
                    if($id != 0){
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\type',$messages['LOG_UPDATE_PRODUCT_TYPE_FAIL'].$id);
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['UPDATE_FAIL']);
                    }else{
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\type',$messages['LOG_INSERT_PRODUCT_TYPE_FAIL'].$id);
                        $view->setVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                        $view->setVariable('form',$form);
                        return $view;
                    }
                }
                if($id != 0){
                    return $this->redirect()->toUrl('/product/type/id/'.$id);
                }else{

                    return $this->redirect()->toUrl('/product/type/id/'.$productTypeTable->getLastInsertValue());
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $view->setVariable('msg',array('danger' => $msg));
                }
                $view->setVariable('form',$form);
                return $view;
            }
        }
        return $view;
    }

    /**
     * Save product type data
     * @param $data
     * @return mixed
     */
    protected function saveProductType($data)
    {
        $productTypeTable = $this->getServiceLocator()->get('ProductTypeTable');
        $dataFinal = $data;
        $type = new ProductType();
        $type->exchangeArray($dataFinal);
        return $productTypeTable->save($type);
    }
    public function deleteTypeAction()
    {
        $this->auth();
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $id = $this->params('id',0);
        $productTypeTable = $this->getServiceLocator()->get('ProductTypeTable');
        if($id != 0){
            $this->deleteType($id,$productTypeTable);
        }
        if(!empty($ids) && is_array($ids)){
            foreach($ids as $id){
                $this->deleteType($id,$productTypeTable);
            }
        }
        return $this->redirect()->toUrl('/product/type');
    }

    /**
     * @param $id
     * @param $table
     * @return bool
     */
    protected function deleteType($id,$table)
    {
        $messages = $this->getMessages();
        $result = $table->clearProductType($id);
        if($result){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete-type',$messages['LOG_DELETE_PRODUCT_TYPE_SUCCESS'].$id);
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete-type',$messages['LOG_DELETE_PRODUCT_TYPE_FAIL'].$id);
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
        return true;
    }
    public function brandAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $brandTable = $this->getServiceLocator()->get('BrandTable');
        $brands = $brandTable->getAvaiableRows();
        $view = new ViewModel();
        $form = new BrandForm($this->getServiceLocator());
        $view->setVariable('form',$form);
        $view->setVariable('brands',$brands);
        $id = $this->params('id',0);
        if($id != 0){
            $brandEntry = $brandTable->getEntry($id);
            if(empty($brandEntry)){
                $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                return $this->redirect()->toUrl('/product/brand');
            }
            $view->setVariable('name',$brandEntry->name);
            $entryParse = (array) $brandEntry;
            $form->setData($entryParse);
        }
        $view->setVariable('id',$id);
        $do = $this->params('do',null);
        $view->setVariable('do',$do);
        if($do != 'add' && $do != null){
            $this->redirect()->toUrl('/product/brand');
        }
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['BRAND_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check condition exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            if($id != 0){
                $exist_valid = new NoRecordExists(array('table' => 'brand','field' => 'name','adapter' => $dbAdapter,'exclude' => array('field' => 'name','value' => $brandEntry->name)));
            }else{
                $exist_valid = new NoRecordExists(array('table' => 'brand','field' => 'name','adapter' => $dbAdapter));
            }
            if(!$exist_valid->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['BRAND_NAME_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/product/brand');
                }
                if($this->saveProductBrand($data)){
                    if($id != 0){
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\brand',$messages['LOG_UPDATE_BRAND_SUCCESS'].$id);
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPDATE_SUCCESS']);
                    }else{
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\brand',$messages['LOG_INSERT_BRAND_SUCCESS'].$brandTable->getLastInsertValue());
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    }
                }else{
                    if($id != 0){
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\brand',$messages['LOG_UPDATE_BRAND_FAIL'].$id);
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['UPDATE_FAIL']);
                    }else{
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\brand',$messages['LOG_INSERT_BRAND_FAIL']);
                        $view->setVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                        $view->setVariable('form',$form);
                        return $view;
                    }
                }
                if($id != 0){
                    return $this->redirect()->toUrl('/product/brand/id/'.$id);
                }else{

                    return $this->redirect()->toUrl('/product/brand/id/'.$brandTable->getLastInsertValue());
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\brand',$msg);
                    $view->setVariable('msg',array('danger' => $msg));
                }
                $view->setVariable('form',$form);
                return $view;
            }
        }
        return $view;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function saveProductBrand($data)
    {
        $brandTable = $this->getServiceLocator()->get('BrandTable');
        $dataFinal = $data;
        $brand = new Brand();
        $brand->exchangeArray($dataFinal);
        return $brandTable->save($brand);
    }
    public function deleteBrandAction()
    {
        $this->auth();
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $id = $this->params('id',0);
        $brandTable = $this->getServiceLocator()->get('BrandTable');
        if($id != 0){
            $this->deleteBrand($id,$brandTable);
        }
        if(!empty($ids) && is_array($ids)){
            foreach($ids as $id){
                $this->deleteBrand($id,$brandTable);
            }
        }
        return $this->redirect()->toUrl('/product/brand');
    }

    /**
     * @param $id
     * @param $table
     * @return bool
     */
    protected function deleteBrand($id,$table)
    {
        $messages = $this->getMessages();
        $result = $table->clearBrand($id);
        if($result){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete-brand',$messages['LOG_DELETE_BRAND_SUCCESS'].$id);
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete-brand',$messages['LOG_DELETE_BRAND_FAIL'].$id);
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
        return true;
    }
    public function exportPriceCompareAction()
    {
        $this->auth();
        $format = $this->params('format');
        if(!$format){
            return true;
        }
        $request = $this->getRequest();
        $ids = $request->getQuery('id');
        $tdm = $this->params('tdm');
        $viewhelperManager = $this->getServiceLocator()->get('viewhelpermanager');
        $priceHelper = $viewhelperManager->get('Price');
        $messages = $this->getMessages();
        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\export-price-compare',$messages['LOG_EXPORT_PRICE_COMPARE'].$tdm);
        $recyclerHelper = $viewhelperManager->get('Recycler');
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        $rowset = $recyclerProductTable->getAvailabeRecyclerProducts($ids);
        $tdmEntry = $tdmProductTable->getEntry($tdm);
        if(!empty($tdmEntry)){
            $tdmExchangePrice = $recyclerProductTable->getSSAPrice($tdmEntry->model,$tdmEntry->condition_id);
            if(empty($tdmEntry)){
                exit();
            }
        }else{
            exit();
        }
        $header = array('Recycler Id','Recycler Name','Country','Product Name','Condition','Date','Price','Price in HKD','%','SSA Price');
        $data = array($header);
        if(!empty($rowset)){
            foreach($rowset as $row){
                $rowParse = array();
                $rowParse[] = $row->recycler_id;
                $rowParse[] = $recyclerHelper->getName($row->recycler_id);
                $rowParse[] = $recyclerHelper->getCountryName($row->recycler_id);
                $rowParse[] = $row->name;
                $rowParse[] = $viewhelperManager->get('condition')->implement($row->condition_id,false);
                $rowParse[] = (!empty($row->date)) ? date('d-m-Y',$row->date) : 'N/A';
                $rowParse[] = $priceHelper->format($row->price);
                $rowParse[] = $priceHelper->format($priceHelper->getExchange($row->price,$row->currency));
                $rowParse[] = $priceHelper->getPercent($priceHelper->getExchange($row->price,$row->currency),$tdmExchangePrice);
                $rowParse[] = $tdmExchangePrice;
                $data[] = $rowParse;
            }
        }
        if(!empty($data)){
            $parseProductName = SlugFile::parseFilename((string) $tdmEntry->name);
            $filename = $parseProductName.'_price_compare_'.date('Y_m_d');
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
        }
        exit();
    }
    public function historicalAction()
    {
        $this->auth();
        $this->layout('layout/empty');
        $request = $this->getRequest();
        $id = $this->params('product',null);
        $searchBy = $this->params('search',null);
        $start = $this->params('start',null);
        $end = $this->params('end',null);
        if($searchBy == null || $id == null || $start == null){
            echo 'Can\'t load data';
            exit();
        }
        $startTime1 = \DateTime::createFromFormat('d-m-Y H:i:s',$start.' 00:00:00');
        $startTime = $startTime1->getTimestamp();
        if($end != null && $end != ''){
            $endTime1 = \DateTime::createFromFormat('d-m-Y H:i:s',$end.' 00:00:00');
            $endTime = $endTime1->getTimestamp();
        }else{
            $endTime = null;
        }

        $recyclerProductTable = $this->getServiceLocator()->get('RecyclerProductTable');
        $tdmProductTable = $this->getServiceLocator()->get('TdmProductTable');
        $exchangeTable = $this->getServiceLocator()->get('ExchangeRateTable');
        $entry = $tdmProductTable->getEntry($id);
        if(empty($entry)){
            echo 'Can\'t load data';
            exit();
        }
        $view = new ViewModel();
        $view->setVariable('search',$searchBy);
        $view->setVariable('id',$id);
        $view->setVariable('start',$startTime);
        $view->setVariable('end',$endTime);
        $viewhelperManager = $this->getServiceLocator()->get('viewhelpermanager');
        $productWithSameModel = $recyclerProductTable->getRowsByModel($entry->model,$entry->condition_id);
        $productsCurrency = array();
        if(!empty($productWithSameModel)){
            foreach($productWithSameModel as $product){
                $countryId = $viewhelperManager->get('Recycler')->getCountryId($product->recycler_id);
                $productsCurrency[$product->product_id] = array('country_id' => $countryId,'recycler_id' => $product->recycler_id,'currency' => $product->currency,'price' => $product->price);
            }
        }
        /**
         * Get the highest one
         */
        $highest = array();
        if(!empty($productsCurrency)){
            $highest = $viewhelperManager->get('Product')->getHighestPrice($productsCurrency,$startTime,$endTime);
        }
        if($searchBy == 'highest'){
            $view->setVariable('highest',$highest);
        }elseif($searchBy == 'country'){
            $country = $this->params('country',0);
            $productsExchangePrice = array();
            $productsExchangeDate = array();
            $productsExchangeRate = array();
            if(!empty($productsCurrency)){
                foreach($productsCurrency as $product_id=>$val){
                    /**
                     * Get echange rate in time range
                     */
                    if((int)$val['country_id'] == (int) $country){
                        $rowset = $exchangeTable->getHighestExchangeByCurrency($val['currency'],$startTime,$endTime);
                        if(!empty($rowset)){
                            $productsExchangePrice[$product_id] = ((float)$val['price'])*((float)$rowset->exchange_rate);
                            $productsExchangeDate[$product_id] = $rowset->time;
                            $productsExchangeRate[$product_id] = $rowset->exchange_rate;
                        }else{
                            $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                            if(!empty($currentExchange)){
                                $productsExchangePrice[$product_id] = ((float)$val['price'])*((float)$currentExchange->exchange_rate);
                                $productsExchangeDate[$product_id] = $currentExchange->time;
                                $productsExchangeRate[$product_id] = $currentExchange->exchange_rate;
                            }else{
                                $productsExchangePrice[$product_id] = (float)$val['price'];
                                $productsExchangeDate[$product_id] = null;
                                $productsExchangeRate[$product_id] = null;
                            }
                        }
                    }
                }
                arsort($productsExchangePrice);
                $highestInCountry = array();
                if(!empty($productsExchangePrice)){
                    foreach($productsExchangePrice as $product_id=>$price){
                        $highestInCountry = array(
                            'product_id' => $product_id,
                            'exchange_price' => $price,
                            'price' => $productsCurrency[$product_id]['price'],
                            'currency' => $productsCurrency[$product_id]['currency'],
                            'time' => $productsExchangeDate[$product_id],
                            'exchange_rate' => $productsExchangeRate[$product_id],
                            'country_id' => $country,
                            'recycler_id' => $productsCurrency[$product_id]['recycler_id']
                        );
                        break;
                    }
                }
                $view->setVariable('highest',$highest);
                $view->setVariable('highestInCountry',$highestInCountry);
            }
        }elseif($searchBy == 'recycler'){
            $recycler_id = $this->params('recycler',0);
            $productsExchangePrice = array();
            $productsExchangeDate = array();
            $productsExchangeRate = array();
            if(!empty($productsCurrency)){
                foreach($productsCurrency as $product_id=>$val){
                    /**
                     * Get echange rate in time range
                     */
                    if((int) $val['recycler_id'] == (int) $recycler_id){
                        $rowset = $exchangeTable->getExchangeByCurrency($val['currency'],$startTime,$endTime);
                        if(!empty($rowset)){
                            foreach($rowset as $row){
                                $productsExchangePrice[$product_id][] = ((float)$val['price'])*((float)$row->exchange_rate);
                                $productsExchangeRate[$product_id][] = $row->exchange_rate;
                                $productsExchangeDate[$product_id][] = $row->time;
                            }
                        }else{
                            $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                            if(!empty($currentExchange)){
                                $productsExchangePrice[$product_id][] = ((float)$val['price'])*((float)$currentExchange->exchange_rate);
                                $productsExchangeRate[$product_id][] = $currentExchange->exchange_rate;
                                $productsExchangeDate[$product_id][] = $currentExchange->time;
                            }else{
                                $productsExchangePrice[$product_id][] = (float)$val['price'];
                                $productsExchangeRate[$product_id][] = null;
                                $productsExchangeDate[$product_id][] = null;
                            }
                        }
                    }
                }
                $highestInRecycler = array();
                $chartFormat = array();
                if(!empty($productsExchangePrice)){
                    foreach($productsExchangePrice as $product_id=>$rateArray){
                        foreach($rateArray as $key=>$price){
                            $highestInRecycler[] = array(
                                'product_id' => $product_id,
                                'exchange_price' => $price,
                                'price' => $productsCurrency[$product_id]['price'],
                                'currency' => $productsCurrency[$product_id]['currency'],
                                'time' => $viewhelperManager->get('product')->getRecyclerProductDate($product_id),
                                'exchange_rate' => $productsExchangeRate[$product_id][$key],
                                'country_id' => $productsCurrency[$product_id]['country_id'],
                                'recycler_id' => $recycler_id
                            );
                            $chartFormat[$product_id][] = array(
                                'exchange_price' => $price,
                                'price' => $productsCurrency[$product_id]['price'],
                                'currency' => $productsCurrency[$product_id]['currency'],
                                'time' => $viewhelperManager->get('product')->getRecyclerProductDate($product_id),
                                'exchange_rate' => $productsExchangeRate[$product_id][$key],
                                'country_id' => $productsCurrency[$product_id]['country_id'],
                                'recycler_id' => $recycler_id
                            );
                        }
                    }
                }
                $view->setVariable('highest',$highest);
                $view->setVariable('productsExchangePrice',$productsExchangePrice);
                $view->setVariable('productsExchangeDate',$productsExchangeDate);
                $view->setVariable('highestInRecycler',$highestInRecycler);
            }
        }elseif($searchBy == 'multi-recycler'){
            $ids = $request->getQuery('multirecycler',null);
            if(!empty($ids)){
                $productsExchangePrice = array();
                $productsExchangeDate = array();
                $productsExchangeRate = array();
                if(!empty($productsCurrency)){
                    foreach($productsCurrency as $product_id=>$val){
                        /**
                         * Get echange rate in time range
                         */
                        if(in_array($val['recycler_id'],$ids)){
                            $rowset = $exchangeTable->getExchangeByCurrency($val['currency'],$startTime,$endTime);
                            if(!empty($rowset)){
                                foreach($rowset as $row){
                                    $productsExchangePrice[$product_id][] = ((float)$val['price'])*((float)$row->exchange_rate);
                                    $productsExchangeRate[$product_id][] = $row->exchange_rate;
                                    $productsExchangeDate[$product_id][] = $row->time;
                                }
                            }else{
                                $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                                if(!empty($currentExchange)){
                                    $productsExchangePrice[$product_id][] = ((float)$val['price'])*((float)$currentExchange->exchange_rate);
                                    $productsExchangeRate[$product_id][] = $currentExchange->exchange_rate;
                                    $productsExchangeDate[$product_id][] = $currentExchange->time;
                                }else{
                                    $productsExchangePrice[$product_id][] = (float)$val['price'];
                                    $productsExchangeRate[$product_id][] = null;
                                    $productsExchangeDate[$product_id][] = null;
                                }
                            }
                        }
                    }
                    $highestInRecycler = array();
                    if(!empty($productsExchangePrice)){
                        foreach($productsExchangePrice as $product_id=>$rateArray){
                            foreach($rateArray as $key=>$price){
                                $highestInRecycler[] = array(
                                    'product_id' => $product_id,
                                    'exchange_price' => $price,
                                    'price' => $productsCurrency[$product_id]['price'],
                                    'currency' => $productsCurrency[$product_id]['currency'],
                                    'time' => $viewhelperManager->get('product')->getRecyclerProductDate($product_id),
                                    'exchange_rate' => $productsExchangeRate[$product_id][$key],
                                    'country_id' => $productsCurrency[$product_id]['country_id'],
                                    'recycler_id' => $productsCurrency[$product_id]['recycler_id']
                                );
                            }
                        }
                    }
                    $view->setVariable('productsExchangePrice',$productsExchangePrice);
                    $view->setVariable('productsExchangeDate',$productsExchangeDate);
                    $view->setVariable('highest',$highest);
                    $view->setVariable('highestInRecycler',$highestInRecycler);
                }
            }
        }
        $view->setVariable('highest',$highest);
        return $view;
    }
    public function exportHistoricalAction()
    {
        $this->auth();
        $this->layout('layout/empty');
        $request = $this->getRequest();
        $id = $this->params('product',null);
        $searchBy = $this->params('search',null);
        $start = $this->params('start',null);
        $end = $this->params('end',null);
        $format = $this->params('format',null);
        if($searchBy == null || $id == null || $start == null || $format == null){
            exit();
        }
        $messages = $this->getMessages();
        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\export-historical',$messages['LOG_EXPORT_HISTORICAL_PRICE'].$id);
        $startTime1 = \DateTime::createFromFormat('d-m-Y H:i:s',$start.' 00:00:00');
        $startTime = $startTime1->getTimestamp();
        if($end != null && $end != ''){
            $endTime1 = \DateTime::createFromFormat('d-m-Y H:i:s',$end.' 00:00:00');
            $endTime = $endTime1->getTimestamp();
        }else{
            $endTime = null;
        }

        $recyclerProductTable = $this->getServiceLocator()->get('RecyclerProductTable');
        $tdmProductTable = $this->getServiceLocator()->get('TdmProductTable');
        $exchangeTable = $this->getServiceLocator()->get('ExchangeRateTable');
        $entry = $tdmProductTable->getEntry($id);
        if(empty($entry)){
            echo 'Can\'t load data';
            exit();
        }
        $viewhelperManager = $this->getServiceLocator()->get('viewhelpermanager');
        $productWithSameModel = $recyclerProductTable->getRowsByModel($entry->model,$entry->condition_id);
        $productsCurrency = array();
        if(!empty($productWithSameModel)){
            foreach($productWithSameModel as $product){
                $countryId = $viewhelperManager->get('Recycler')->getCountryId($product->recycler_id);
                $productsCurrency[$product->product_id] = array('country_id' => $countryId,'recycler_id' => $product->recycler_id,'currency' => $product->currency,'price' => $product->price);
            }
        }
        /**
         * Get the highest one
         */
        $highest = array();
        if(!empty($productsCurrency)){
            $highest = $viewhelperManager->get('Product')->getHighestPrice($productsCurrency,$startTime,$endTime);
        }
        if($searchBy == 'highest'){
            /**
             * Export highest one
             */
            $header = array('Date','Model Name','Condition','Recycler Detail','Price','Currency','Exchange Rate','Price in HKD','Highest Recycler Price');
            $data = array($header);
            if(!empty($highest)){
                $rowParse = array();
                $rowParse[] = (!empty($highest['time'])) ? date('d-m-Y',(int)$highest['time']) : 'N/A';
                $rowParse[] = $viewhelperManager->get('Product')->getRecyclerProductName($highest['product_id']);
                $rowParse[] = $viewhelperManager->get('Product')->getRecyclerProductCondition($highest['product_id']);
                $rowParse[] = $viewhelperManager->get('Recycler')->getRecyclerDetail($highest['recycler_id']);
                $rowParse[] = $viewhelperManager->get('Price')->format($highest['price']);
                $rowParse[] = $highest['currency'];
                $rowParse[] = $viewhelperManager->get('Price')->format($highest['exchange_rate']);
                $rowParse[] = $viewhelperManager->get('Price')->format($highest['exchange_price']);
                $rowParse[] = $viewhelperManager->get('Price')->format($highest['exchange_price']);
                $data[] = $rowParse;
            }
            if(!empty($data)){
                $parseProductName = SlugFile::parseFilename($entry->name);
                $filename = $parseProductName.'_highest_price_export_'.date('Y_m_d');
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
                exit();
            }
        }elseif($searchBy == 'country'){
            $country = $this->params('country',0);
            $productsExchangePrice = array();
            $productsExchangeDate = array();
            $productsExchangeRate = array();
            if(!empty($productsCurrency)){
                foreach($productsCurrency as $product_id=>$val){
                    /**
                     * Get echange rate in time range
                     */
                    if((int)$val['country_id'] == (int) $country){
                        $rowset = $exchangeTable->getHighestExchangeByCurrency($val['currency'],$startTime,$endTime);
                        if(!empty($rowset)){
                            $productsExchangePrice[$product_id] = ((float)$val['price'])*((float)$rowset->exchange_rate);
                            $productsExchangeDate[$product_id] = $rowset->time;
                            $productsExchangeRate[$product_id] = $rowset->exchange_rate;
                        }else{
                            $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                            if(!empty($currentExchange)){
                                $productsExchangePrice[$product_id] = ((float)$val['price'])*((float)$currentExchange->exchange_rate);
                                $productsExchangeDate[$product_id] = $currentExchange->time;
                                $productsExchangeRate[$product_id] = $currentExchange->exchange_rate;
                            }else{
                                $productsExchangePrice[$product_id] = (float)$val['price'];
                                $productsExchangeDate[$product_id] = null;
                                $productsExchangeRate[$product_id] = null;
                            }
                        }
                    }
                }
                arsort($productsExchangePrice);
                $highestInCountry = array();
                if(!empty($productsExchangePrice)){
                    foreach($productsExchangePrice as $product_id=>$price){
                        $highestInCountry = array(
                            'product_id' => $product_id,
                            'exchange_price' => $price,
                            'price' => $productsCurrency[$product_id]['price'],
                            'currency' => $productsCurrency[$product_id]['currency'],
                            'time' => $viewhelperManager->get('product')->getRecyclerProductDate($product_id),
                            'exchange_rate' => $productsExchangeRate[$product_id],
                            'country_id' => $country,
                            'recycler_id' => $productsCurrency[$product_id]['recycler_id']
                        );
                        break;
                    }
                }
                /**
                 * Export highest one
                 */
                $header = array('Date','Model Name','Condition','Recycler Detail','Price','Currency','Exchange Rate','Price in HKD','Highest Recycler Price');
                $data = array($header);
                if(!empty($highestInCountry)){
                    $rowParse = array();
                    $rowParse[] = (!empty($highestInCountry['time'])) ? date('d-m-Y',(int)$highestInCountry['time']) : 'N/A';
                    $rowParse[] = $viewhelperManager->get('Product')->getRecyclerProductName($highestInCountry['product_id']);
                    $rowParse[] = $viewhelperManager->get('Product')->getRecyclerProductCondition($highestInCountry['product_id']);
                    $rowParse[] = $viewhelperManager->get('Recycler')->getRecyclerDetail($highestInCountry['recycler_id']);
                    $rowParse[] = $viewhelperManager->get('Price')->format($highestInCountry['price']);
                    $rowParse[] = $highestInCountry['currency'];
                    $rowParse[] = $viewhelperManager->get('Price')->format($highestInCountry['exchange_rate']);
                    $rowParse[] = $viewhelperManager->get('Price')->format($highestInCountry['exchange_price']);
                    if(!empty($highest)){
                        $rowParse[] = $viewhelperManager->get('Price')->format($highest['exchange_price']);
                    }else{
                        $rowParse[] = '';
                    }
                    $data[] = $rowParse;
                }
                if(!empty($data)){
                    $parseProductName = SlugFile::parseFilename($entry->name);
                    $filename = $parseProductName.'_highest_price_export_'.date('Y_m_d');
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
                    exit();
                }
            }
        }elseif($searchBy == 'recycler'){
            $recycler_id = $this->params('recycler',0);
            $productsExchangePrice = array();
            $productsExchangeDate = array();
            $productsExchangeRate = array();
            if(!empty($productsCurrency)){
                foreach($productsCurrency as $product_id=>$val){
                    /**
                     * Get echange rate in time range
                     */
                    if((int) $val['recycler_id'] == (int) $recycler_id){
                        $rowset = $exchangeTable->getExchangeByCurrency($val['currency'],$startTime,$endTime);
                        if(!empty($rowset)){
                            foreach($rowset as $row){
                                $productsExchangePrice[$product_id][] = ((float)$val['price'])*((float)$row->exchange_rate);
                                $productsExchangeRate[$product_id][] = $row->exchange_rate;
                                $productsExchangeDate[$product_id][] = $row->time;
                            }
                        }else{
                            $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                            if(!empty($currentExchange)){
                                $productsExchangePrice[$product_id][] = ((float)$val['price'])*((float)$currentExchange->exchange_rate);
                                $productsExchangeRate[$product_id][] = $currentExchange->exchange_rate;
                                $productsExchangeDate[$product_id][] = $currentExchange->time;
                            }else{
                                $productsExchangePrice[$product_id][] = (float)$val['price'];
                                $productsExchangeRate[$product_id][] = null;
                                $productsExchangeDate[$product_id][] = null;
                            }
                        }
                    }
                }
                $highestInRecycler = array();
                $chartFormat = array();
                if(!empty($productsExchangePrice)){
                    foreach($productsExchangePrice as $product_id=>$rateArray){
                        foreach($rateArray as $key=>$price){
                            $highestInRecycler[] = array(
                                'product_id' => $product_id,
                                'exchange_price' => $price,
                                'price' => $productsCurrency[$product_id]['price'],
                                'currency' => $productsCurrency[$product_id]['currency'],
                                'time' => $viewhelperManager->get('product')->getRecyclerProductDate($product_id),
                                'exchange_rate' => $productsExchangeRate[$product_id][$key],
                                'country_id' => $productsCurrency[$product_id]['country_id'],
                                'recycler_id' => $recycler_id
                            );
                            $chartFormat[$product_id][] = array(
                                'exchange_price' => $price,
                                'price' => $productsCurrency[$product_id]['price'],
                                'currency' => $productsCurrency[$product_id]['currency'],
                                'time' => $viewhelperManager->get('product')->getRecyclerProductDate($product_id),
                                'exchange_rate' => $productsExchangeRate[$product_id][$key],
                                'country_id' => $productsCurrency[$product_id]['country_id'],
                                'recycler_id' => $recycler_id
                            );
                        }
                    }
                }
                /**
                 * Export highest one
                 */
                $header = array('Date','Model Name','Condition','Recycler Detail','Price','Currency','Exchange Rate','Price in HKD','Highest Recycler Price');
                $data = array($header);
                if(!empty($highestInRecycler)){
                    foreach($highestInRecycler as $h){
                        $rowParse = array();
                        $rowParse[] = (!empty($h['time'])) ? date('d-m-Y',(int)$h['time']) : 'N/A';
                        $rowParse[] = $viewhelperManager->get('Product')->getRecyclerProductName($h['product_id']);
                        $rowParse[] = $viewhelperManager->get('Product')->getRecyclerProductCondition($h['product_id']);
                        $rowParse[] = $viewhelperManager->get('Recycler')->getRecyclerDetail($h['recycler_id']);
                        $rowParse[] = $viewhelperManager->get('Price')->format($h['price']);
                        $rowParse[] = $h['currency'];
                        $rowParse[] = $viewhelperManager->get('Price')->format($h['exchange_rate']);
                        $rowParse[] = $viewhelperManager->get('Price')->format($h['exchange_price']);
                        if(!empty($highest)){
                            $rowParse[] = $viewhelperManager->get('Price')->format($highest['exchange_price']);
                        }else{
                            $rowParse[] = '';
                        }
                        $data[] = $rowParse;
                    }
                }
                if(!empty($data)){
                    $parseProductName = SlugFile::parseFilename($entry->name);
                    $filename = $parseProductName.'_highest_price_export_'.date('Y_m_d');
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
                    exit();
                }
            }
        }elseif($searchBy == 'multi-recycler'){
            $ids = $request->getQuery('multirecycler',null);
            if(!empty($ids)){
                $productsExchangePrice = array();
                $productsExchangeDate = array();
                $productsExchangeRate = array();
                if(!empty($productsCurrency)){
                    foreach($productsCurrency as $product_id=>$val){
                        /**
                         * Get echange rate in time range
                         */
                        if(in_array($val['recycler_id'],$ids)){
                            $rowset = $exchangeTable->getExchangeByCurrency($val['currency'],$startTime,$endTime);
                            if(!empty($rowset)){
                                foreach($rowset as $row){
                                    $productsExchangePrice[$product_id][] = ((float)$val['price'])*((float)$row->exchange_rate);
                                    $productsExchangeRate[$product_id][] = $row->exchange_rate;
                                    $productsExchangeDate[$product_id][] = $row->time;
                                }
                            }else{
                                $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                                if(!empty($currentExchange)){
                                    $productsExchangePrice[$product_id][] = ((float)$val['price'])*((float)$currentExchange->exchange_rate);
                                    $productsExchangeRate[$product_id][] = $currentExchange->exchange_rate;
                                    $productsExchangeDate[$product_id][] = $currentExchange->time;
                                }else{
                                    $productsExchangePrice[$product_id][] = (float)$val['price'];
                                    $productsExchangeRate[$product_id][] = null;
                                    $productsExchangeDate[$product_id][] = null;
                                }
                            }
                        }
                    }
                    $highestInRecycler = array();
                    if(!empty($productsExchangePrice)){
                        foreach($productsExchangePrice as $product_id=>$rateArray){
                            foreach($rateArray as $key=>$price){
                                $highestInRecycler[] = array(
                                    'product_id' => $product_id,
                                    'exchange_price' => $price,
                                    'price' => $productsCurrency[$product_id]['price'],
                                    'currency' => $productsCurrency[$product_id]['currency'],
                                    'time' => $viewhelperManager->get('product')->getRecyclerProductDate($product_id),
                                    'exchange_rate' => $productsExchangeRate[$product_id][$key],
                                    'country_id' => $productsCurrency[$product_id]['country_id'],
                                    'recycler_id' => $productsCurrency[$product_id]['recycler_id']
                                );
                            }
                        }
                    }
                    /**
                     * Export highest one
                     */
                    $header = array('Date','Model Name','Condition','Recycler Detail','Price','Currency','Exchange Rate','Price in HKD','Highest Recycler Price');
                    $data = array($header);
                    if(!empty($highestInRecycler)){
                        foreach($highestInRecycler as $h){
                            $rowParse = array();
                            $rowParse[] = (!empty($h['time'])) ? date('d-m-Y',(int)$h['time']) : 'N/A';
                            $rowParse[] = $viewhelperManager->get('Product')->getRecyclerProductName($h['product_id']);
                            $rowParse[] = $viewhelperManager->get('Product')->getRecyclerProductCondition($h['product_id']);
                            $rowParse[] = $viewhelperManager->get('Recycler')->getRecyclerDetail($h['recycler_id']);
                            $rowParse[] = $viewhelperManager->get('Price')->format($h['price']);
                            $rowParse[] = $h['currency'];
                            $rowParse[] = $viewhelperManager->get('Price')->format($h['exchange_rate']);
                            $rowParse[] = $viewhelperManager->get('Price')->format($h['exchange_price']);
                            if(!empty($highest)){
                                $rowParse[] = $viewhelperManager->get('Price')->format($highest['exchange_price']);
                            }else{
                                $rowParse[] = '';
                            }
                            $data[] = $rowParse;
                        }
                    }
                    if(!empty($data)){
                        $parseProductName = SlugFile::parseFilename($entry->name);
                        $filename = $parseProductName.'_highest_price_export_'.date('Y_m_d');
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
                        exit();
                    }
                }
            }
        }
        exit();
    }
    public function selectRecyclerAction()
    {
        $this->auth();
        $country = $this->params('country',0);
        if($country == 0){
            echo '<option value="0">Select Country</option>';
            exit();
        }
        $reyclerTable = $this->getServiceLocator()->get('RecyclerTable');
        $rowset = $reyclerTable->getRecyclersByCountry($country);
        $html = '<option value="0">Select Country</option>';
        if(!empty($rowset)){
            foreach($rowset as $row){
                $html .= '<option value="'.$row->recycler_id.'">'.$row->name.'</option>';
            }
        }
        echo $html;
        exit();
    }
    public function popularAction()
    {
        $this->auth();
        $cache = CacheSerializer::init();
        $request = $this->getRequest();
        if($request->isPost())
        {
            $post = $request->getPost()->toArray();
            if(empty($post['start']) || $post['start'] == ''){
                $this->flashMessenger()->setNamespace('error')->addMessage('Please set start time');
                return $this->redirect()->toUrl('/product/popular');
            }
            if(empty($post['products']) || $post['products'] == ''){
                $this->flashMessenger()->setNamespace('error')->addMessage('Please select products');
                return $this->redirect()->toUrl('/product/popular');
            }
            $data = $post;
            $cache->removeItem('popular');
            $cache->addItem('popular',$data);
            $this->flashMessenger()->setNamespace('success')->addMessage('Update popular products success');
            return $this->redirect()->toUrl('/product/popular');
        }
        $view = new ViewModel();
        $popular = $cache->getItem('popular');
        $view->setVariable('popular',$popular);
        return $view;
    }
}
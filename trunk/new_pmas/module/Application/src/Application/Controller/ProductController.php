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
use Core\Cache\CacheSerializer;
use Core\Controller\AbstractController;
use Core\Model\Brand;
use Core\Model\CreatePath;
use Core\Model\ProductType;
use Core\Model\SlugFile;
use Core\Model\TdmProduct;
use Zend\Debug\Debug;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\Db\RecordExists;
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
        parent::initAction();
        $request = $this->getRequest();
        $ppp = $request->getQuery('ppp');
        if(!empty($ppp)){
            $this->getViewHelperPlugin('core')->setItemPerPage($ppp);
        }
        $item_per_page = $this->getViewHelperPlugin('core')->getItemPerPage();
        $page = trim($this->params('page',1),'/');
        $tdmProductTable = $this->sm->get('TdmProductTable');
        $select = $tdmProductTable->getTdmProductQuery();
        $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
        $paginator = new Paginator(new DbSelect($select,$dbAdapter));
        $paginator->setItemCountPerPage($item_per_page);
        $paginator->setCurrentPageNumber($page);
        $this->setViewVariable('paginator', $paginator);
        $country = $this->params('country',null);
        $this->setViewVariable('country',$country);
        $this->setViewVariable('current_page',$page);
        $this->setViewVariable('item_per_page',$item_per_page);
        return $this->view;
    }
    public function filterAction()
    {
        parent::initAction();
        $higher = $this->params('higher',null);
        $country = $this->params('country',null);
        $recycler_country = $this->params('recycler-country',null);
        $this->setViewVariable('higher',$higher);
        $this->setViewVariable('country',$country);
        $this->setViewVariable('recycler_country',$recycler_country);
        $messages = $this->getMessages();
        $this->getViewHelperPlugin('user')->log('application\\product\\filter',$messages['LOG_VIEW_PRODUCT_FILTER']);
        $recyclerProductTable = $this->sm->get('RecyclerProductTable');
        $request = $this->getRequest();
        $ppp = $request->getQuery('ppp');
        if(!empty($ppp)){
            $this->getViewHelperPlugin('core')->setItemPerPage($ppp);
        }
        $tdmProductTable = $this->sm->get('TdmProductTable');
        $item_per_page = $this->getViewHelperPlugin('core')->getItemPerPage();
        $page = trim($this->params('page',1),'/');
        if($higher != null){
            /*$select = $tdmProductTable->getTdmProductQuery();
            $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
            $paginator = new Paginator(new DbSelect($select,$dbAdapter));*/
            $finalRow = array();
            $rowset = $tdmProductTable->getAvaiableRows();
            foreach($rowset as $row){
                $data = array();
                $ssa_price = $this->getViewHelperPlugin('product')->getSSAPrice($row->model,$row->condition_id);
                $recyclerProducts = $this->getViewHelperPlugin('product')->getRowsMatching($row->model,$row->condition_id,3,$recycler_country);
                if(!empty($recyclerProducts)){
                    foreach($recyclerProducts as $rp){
                        $currentExchange = $this->getViewHelperPlugin('exchange')->getCurrentExchangeOfCurrency($rp->currency);
                        $priceExchange = ((float) $rp->price )/ $currentExchange;
                        if($ssa_price != 0){
                            $percentage = (($priceExchange-$ssa_price)/$ssa_price)*100;
                            if($percentage >= (int) $higher){
                                $data[] = array(
                                    'recycler' => $this->getViewHelperPlugin('recycler')->getName($rp->recycler_id),
                                    'price' => $priceExchange,
                                    'percentag' => $percentage,
                                );
                            }
                        }
                    }
                }
                if(!empty($data)){
                    $finalRow[] = $row;
                }
            }
            $paginator = new Paginator(new ArrayAdapter($finalRow));
        }else{
            if($country != null){
                $select = $tdmProductTable->getProductsByCountryQuery($country);
            }else{
                $select = $tdmProductTable->getTdmProductQuery();
            }
            $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
            $paginator = new Paginator(new DbSelect($select,$dbAdapter));
        }
        /*Debug::dump($recycler_country);die;*/
        $paginator->setItemCountPerPage($item_per_page);
        $paginator->setCurrentPageNumber($page);
        $this->setViewVariable('paginator', $paginator);
        return $this->view;
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/product');
                }
                if($this->save($data)){
                    $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['INSERT_SUCCESS']));
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
                        $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['INSERT_FAIL']));
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
            /*if(!$empty->isValid($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }*/
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
            /*if(!is_numeric($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_VALID']));
                $view->setVariable('form',$form);
                return $view;
            }*/
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
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
                        $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['UPDATE_SUCCESS']));
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
            /*if(!$empty->isValid($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }*/
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
            /*if(!is_numeric($post['price'])){
                $view->setVariable('msg',array('danger' => $messages['PRODUCT_PRICE_NOT_VALID']));
                $view->setVariable('form',$form);
                return $view;
            }*/
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/product');
                }
                if($this->saveTdmProduct($data)){
                    $lastInsertId = $tdmProductTable->getLastInsertValue();
                    $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['INSERT_SUCCESS']));
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
        $recyclerProductsWithSameModel = $recyclerProductTable->getRowsMatching($entry->model,$entry->condition_id);
        $messages = $this->getMessages();
        $exchangeHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('exchange');
        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\detail',$messages['LOG_VIEW_TDM_PRODUCT'].$id);
        if(is_numeric($filter)){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\detail',$messages['LOG_FILTER_HIGHER_TDM_PRODUCT'].$id);
            $ssa_price = (float) $recyclerProductTable->getSSAPrice($entry->model,$entry->condition_id);
            if(!empty($ssa_price)){
                /**
                 * Filter higher than 50%
                 */
                if(!empty($recyclerProductsWithSameModel)){
                    $products = array();
                    foreach($recyclerProductsWithSameModel as $product){
                        $currentExchange = $exchangeHelper->getCurrentExchangeOfCurrency($product->currency);
                        $priceExchange = ((float) $product->price) / $currentExchange;
                        if($ssa_price != 0){
                            $percentage = (($priceExchange-$ssa_price)/$ssa_price)*100;
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
        $messages = $this->getMessages();
        $id = $this->params('id',0);
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        if($id != 0){
            if($this->delete($id,$tdmProductTable)){
                $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['DELETE_SUCCESS']));
            }else{
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['DELETE_FAIL']));
            }
        }
        if(!empty($ids) && is_array($ids)){
            $i = 0;
            foreach($ids as $id){
                if($this->delete($id,$tdmProductTable)){
                    $i++;
                }
            }
            $this->flashMessenger()->setNamespace('success')->addMessage($i.$this->__($messages['QTY_PRODUCTS_DELETE_SUCCESS']));
        }
        return $this->redirect()->toUrl('/product');
    }
    protected function delete($id,$table)
    {
        $messages = $this->getMessages();
        if($table->deleteEntry($id)){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete',$messages['LOG_DELETE_TDM_PRODUCT_SUCCESS'].$id);
            return true;
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete',$messages['LOG_DELETE_TDM_PRODUCT_FAILT'].$id);
            return false;
        }
    }
    public function exportAction()
    {
        parent::initAction();
        $messages = $this->getMessages();
        $format = $this->params('format');
        $country = $this->params('country');
        $recycler_country = $this->params('recycler-country');
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
        $rowset = $this->productTable->getProductsFilterExport($ids,$country);
        $header = array(
            $this->__('Product ID'),
            $this->__('Brand'),
            $this->__('Name'),
            $this->__('Condition'),
            $this->__('Product type'),
            $this->__('Country'),
            $this->__('SSA Price'),
            $this->__('Recycler 1'),
            $this->__('Price in HKD 1'),
            $this->__('Percentage 1'),
            $this->__('Recycler 2'),
            $this->__('Price in HKD 2'),
            $this->__('Percentage 2'),
            $this->__('Recycler 3'),
            $this->__('Price in HKD 3'),
            $this->__('Percentage 3')
        );
        $data = array($header);
        if(!empty($rowset)){
            foreach($rowset as $row){
                $rowParse = array();
                $rowParse[] = $row->product_id;
                $rowParse[] = $viewhelperManager->get('product_brand')->getName($row->brand_id);
                $rowParse[] = $row->name;
                $rowParse[] = $viewhelperManager->get('Condition')->implement($row->condition_id);
                $rowParse[] = $viewhelperManager->get('product_type')->getName($row->type_id);
                $rowParse[] = $viewhelperManager->get('Country')->implement($row->country_id);
                $price_data = array();
                $ssa_price = $this->getViewHelperPlugin('product')->getSSAPrice($row->model,$row->condition_id);
                if(!empty($ssa_price)){
                    $rowParse[] = $this->getViewHelperPlugin('price')->formatCurrency($ssa_price,'HKD');
                }else{
                    $rowParse[] = '';
                }
                $recyclerProducts = $this->getViewHelperPlugin('product')->getRowsMatching($row->model,$row->condition_id,3,$recycler_country);
                if(!empty($recyclerProducts)){
                    foreach($recyclerProducts as $rp){
                        $currentExchange = $this->getViewHelperPlugin('exchange')->getCurrentExchangeOfCurrency($rp->currency);
                        $priceExchange = ((float) $rp->price )/ $currentExchange;
                        if($ssa_price != 0){
                            $percentage = (($priceExchange-$ssa_price)/$ssa_price)*100;
                        }else{
                            $percentage = 'N/A';
                        }
                        $price_data[] = array(
                            'recycler' => $this->getViewHelperPlugin('recycler')->getName($rp->recycler_id),
                            'price' => $priceExchange,
                            'percentag' => $percentage,
                        );
                    }
                }
                if(!empty($price_data)){
                    $i = 0;
                    foreach($price_data as $item){
                        $rowParse[] = $item['recycler'];
                        $rowParse[] = $this->getViewHelperPlugin('price')->formatCurrency($item['price'],'HKD');
                        $rowParse[] = (is_numeric($item['percentag'])) ? $this->getViewHelperPlugin('price')->format($item['percentag']).' %' : $item['percentag'];
                        $i++;
                    }
                    if($i==1){
                        $rowParse[] = '';
                        $rowParse[] = '';
                        $rowParse[] = '';
                        $rowParse[] = '';
                        $rowParse[] = '';
                        $rowParse[] = '';
                    }elseif($i == 2){
                        $rowParse[] = '';
                        $rowParse[] = '';
                        $rowParse[] = '';
                    }
                }else{
                    $rowParse[] = '';
                    $rowParse[] = '';
                    $rowParse[] = '';
                    $rowParse[] = '';
                    $rowParse[] = '';
                    $rowParse[] = '';
                    $rowParse[] = '';
                    $rowParse[] = '';
                    $rowParse[] = '';
                }
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
            $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['EXPORT_FAIL']));
            return $this->redirect()->toUrl('/product');
        }
        exit();
    }
    public function importAction()
    {
        parent::initAction();
        $request = $this->getRequest();
        $messages = $this->getMessages();
        if($request->isPost()){
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $path = getcwd() . "/upload/import";
            CreatePath::createPath($path);
            if(empty($post)){
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                return $this->redirect()->toUrl('/recycler');
            }
            if($post['upload_file']['name'] && trim($post['upload_file']['name']) != ''){
                $ext = pathinfo($post['upload_file']['name'], PATHINFO_EXTENSION);
                $filename = $this->getViewHelperPlugin('Files')->getFilenameUnique($post['upload_file']['name'],$path);
                move_uploaded_file($post['upload_file']['tmp_name'], $path .DIRECTORY_SEPARATOR .$filename);
                $dataImport = array();
                if(strtolower($ext) == 'xlsx'){
                    $file = new \BasicExcel\Reader\Xlsx();
                    $file->load($path .DIRECTORY_SEPARATOR .$post['upload_file']['name']);
                    $dataImport = $file->toArray();
                }elseif(strtolower($ext) == 'xls'){
                    $file = new \BasicExcel\Reader\Xls();
                    $file->read($path .DIRECTORY_SEPARATOR .$post['upload_file']['name']);
                    $dataImport = $file->toArray();
                }else{
                    $file = new \BasicExcel\Reader\Csv();
                    $file->load($path .DIRECTORY_SEPARATOR .$post['upload_file']['name']);
                    $dataImport = $file->toArray();
                }
                $dataParse = array();
                $tdmProduct = new TdmProduct();
                $tdmProductTable = $this->sm->get('TdmProductTable');
                if(empty($dataImport)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirectUrl('/product/index');
                }
                $header = array();
                $first = $dataImport[0];
                foreach($first as $index=>$value){
                    $header[$index] = $this->getViewHelperPlugin('core')->slugify($value);
                }
                /**
                 * If header null, return
                 */
                if(empty($header)){
                    exit();
                }
                /**
                 * Insert new brand
                 */
                $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                $exist_brand = new NoRecordExists(array(
                    'table' => 'brand',
                    'field' => 'name',
                    'adapter' => $dbAdapter,
                ));
                $brandDataSet = new Brand();
                $brandTable = $this->getServiceLocator()->get('BrandTable');
                foreach($dataImport as $i=>$row){
                    if($i>0){
                        $brand = (array_search('brand',$header) !== false) ? $row[array_search('brand',$header)] : null;
                        if(!empty($brand) && $exist_brand->isValid($brand)){
                            /**
                             * Insert new brand
                             */
                            $brandDataSet->exchangeArray(array('name' => $brand));
                            $brandTable->save($brandDataSet);
                        }
                    }
                }
                $data = array();
                foreach($dataImport as $i=>$row){
                    if($i>0){
                        $rowParse = array();
                        $rowParse['product_id'] = (array_search('product-id',$header) !== false) ? $row[array_search('product-id',$header)] : 0;
                        $rowParse['brand_id'] = (array_search('brand',$header) !== false) ? (int) $this->getViewHelperPlugin('product_brand')->getBrandIdByName(trim($row[array_search('brand',$header)])) : 0;
                        $rowParse['model'] = (array_search('model',$header)!== false)  ? $row[array_search('model',$header)] : null;
                        $rowParse['type_id'] = (array_search('product-type',$header) !== false) ? (int) $this->getViewHelperPlugin('product_type')->getTypeIdByName(trim($row[array_search('product-type',$header)])) : 0;
                        $rowParse['country_id'] = (array_search('country',$header) !== false) ? (int)$this->getViewHelperPlugin('country')->getCountryNameById(trim($row[array_search('country',$header)])) : 0;
                        $rowParse['name'] = (array_search('name',$header) !== false) ? $row[array_search('name',$header)] : null;
                        $rowParse['condition_id'] = (array_search('condition',$header) !== false) ? (int)$this->getViewHelperPlugin('condition')->getRecyclerConditionIdByName(trim($row[array_search('condition',$header)])) : 0;
                        $data[] = $rowParse;
                        $tdmProduct->exchangeArray($rowParse);
                        $tdmProductTable->save($tdmProduct);
                    }
                }
                /**
                 * Delete upload file
                 */
                @unlink($path .DIRECTORY_SEPARATOR .$post['upload_file']['name']);
                $this->getViewHelperPlugin('user')->log('application\\product\\import',$messages['LOG_TDM_PRODUCT_IMPORT_SUCCESS']);
                $this->addSuccessFlashMessenger($this->__($messages['UPLOAD_SUCCESS']));
            }else{
                $this->getViewHelperPlugin('user')->log('application\\product\\import',$messages['LOG_TDM_PRODUCT_IMPORT_FAIL']);
                $this->addErrorFlashMessenger($this->__($messages['NO_DATA']));
                return $this->redirectUrl('/product/index');
            }
            return $this->redirectUrl('/product/index');
        }else{
            return $this->redirectUrl('/product/index');
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
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/product/type');
                }
                if($this->saveProductType($data)){
                    if($id != 0){
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\type',$messages['LOG_UPDATE_PRODUCT_TYPE_SUCCESS'].$id);
                        $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['UPDATE_SUCCESS']));
                    }else{
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\type',$messages['LOG_INSERT_PRODUCT_TYPE_SUCCESS'].$productTypeTable->getLastInsertValue());
                        $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['INSERT_SUCCESS']));
                    }
                }else{
                    if($id != 0){
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\type',$messages['LOG_UPDATE_PRODUCT_TYPE_FAIL'].$id);
                        $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['UPDATE_FAIL']));
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
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $id = $this->params('id',0);
        $productTypeTable = $this->getServiceLocator()->get('ProductTypeTable');
        if($id != 0){
            if($this->deleteType($id,$productTypeTable)){
                $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['DELETE_SUCCESS']));
            }else{
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['DELETE_FAIL']));
            }
        }
        if(!empty($ids) && is_array($ids)){
            $i = 0;
            foreach($ids as $id){
                if($this->deleteType($id,$productTypeTable)){
                    $i++;
                }
            }
            $this->flashMessenger()->setNamespace('success')->addMessage($i.$this->__($messages['QTY_PRODUCT_TYPES_DELETE_SUCCESS']));
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
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete-type',$messages['LOG_DELETE_PRODUCT_TYPE_FAIL'].$id);
        }
        return $result;
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
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
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
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['NO_DATA']));
                    return $this->redirect()->toUrl('/product/brand');
                }
                if($this->saveProductBrand($data)){
                    if($id != 0){
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\brand',$messages['LOG_UPDATE_BRAND_SUCCESS'].$id);
                        $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['UPDATE_SUCCESS']));
                    }else{
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\brand',$messages['LOG_INSERT_BRAND_SUCCESS'].$brandTable->getLastInsertValue());
                        $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['INSERT_SUCCESS']));
                    }
                }else{
                    if($id != 0){
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\brand',$messages['LOG_UPDATE_BRAND_FAIL'].$id);
                        $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['UPDATE_FAIL']));
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
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $id = $this->params('id',0);
        $brandTable = $this->getServiceLocator()->get('BrandTable');
        if($id != 0){
            if($this->deleteBrand($id,$brandTable)){
                $this->flashMessenger()->setNamespace('success')->addMessage($this->__($messages['DELETE_SUCCESS']));
            }else{
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__($messages['DELETE_FAIL']));
            }
        }
        if(!empty($ids) && is_array($ids)){
            $i =0 ;
            foreach($ids as $id){
                if($this->deleteBrand($id,$brandTable)){
                    $i++;
                }
            }
            $this->flashMessenger()->setNamespace('error')->addMessage($i.$this->__($messages['QTY_BRANDS_DELETE_SUCCESS']));
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
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\product\\delete-brand',$messages['LOG_DELETE_BRAND_FAIL'].$id);
        }
        return $result;
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
        $header = array(
            $this->__('Recycler Id'),
            $this->__('Recycler Name'),
            $this->__('Country'),
            $this->__('Product Name'),
            $this->__('Condition'),
            $this->__('Date'),
            $this->__('Price'),
            $this->__('Price in HKD'),
            '%',
            $this->__('SSA Price')
        );
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
                            $productsExchangePrice[$product_id] = ((float)$val['price'])/((float)$rowset->exchange_rate);
                            $productsExchangeDate[$product_id] = $rowset->time;
                            $productsExchangeRate[$product_id] = $rowset->exchange_rate;
                        }else{
                            $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                            if(!empty($currentExchange)){
                                $productsExchangePrice[$product_id] = ((float)$val['price'])/((float)$currentExchange->exchange_rate);
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
                                $productsExchangePrice[$product_id][] = ((float)$val['price'])/((float)$row->exchange_rate);
                                $productsExchangeRate[$product_id][] = $row->exchange_rate;
                                $productsExchangeDate[$product_id][] = $row->time;
                            }
                        }else{
                            $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                            if(!empty($currentExchange)){
                                $productsExchangePrice[$product_id][] = ((float)$val['price'])/((float)$currentExchange->exchange_rate);
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
                                    $productsExchangePrice[$product_id][] = ((float)$val['price'])/((float)$row->exchange_rate);
                                    $productsExchangeRate[$product_id][] = $row->exchange_rate;
                                    $productsExchangeDate[$product_id][] = $row->time;
                                }
                            }else{
                                $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                                if(!empty($currentExchange)){
                                    $productsExchangePrice[$product_id][] = ((float)$val['price'])/((float)$currentExchange->exchange_rate);
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
            $header = array($this->__('Date'),
                $this->__('Model Name'),
                $this->__('Condition'),
                $this->__('Recycler Detail'),
                $this->__('Price'),
                $this->__('Currency'),
                $this->__('Exchange Rate'),
                $this->__('Price in HKD'),
                $this->__('Highest Recycler Price'));
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
                            $productsExchangePrice[$product_id] = ((float)$val['price'])/((float)$rowset->exchange_rate);
                            $productsExchangeDate[$product_id] = $rowset->time;
                            $productsExchangeRate[$product_id] = $rowset->exchange_rate;
                        }else{
                            $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                            if(!empty($currentExchange)){
                                $productsExchangePrice[$product_id] = ((float)$val['price'])/((float)$currentExchange->exchange_rate);
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
                $header = array($this->__('Date'),
                    $this->__('Model Name'),
                    $this->__('Condition'),
                    $this->__('Recycler Detail'),
                    $this->__('Price'),
                    $this->__('Currency'),
                    $this->__('Exchange Rate'),
                    $this->__('Price in HKD'),$this->__('Highest Recycler Price'));
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
                                $productsExchangePrice[$product_id][] = ((float)$val['price'])/((float)$row->exchange_rate);
                                $productsExchangeRate[$product_id][] = $row->exchange_rate;
                                $productsExchangeDate[$product_id][] = $row->time;
                            }
                        }else{
                            $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                            if(!empty($currentExchange)){
                                $productsExchangePrice[$product_id][] = ((float)$val['price'])/((float)$currentExchange->exchange_rate);
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
                $header = array($this->__('Date'),
                    $this->__('Model Name'),
                    $this->__('Condition'),
                    $this->__('Recycler Detail'),
                    $this->__('Price'),
                    $this->__('Currency'),
                    $this->__('Exchange Rate'),
                    $this->__('Price in HKD'),
                    $this->__('Highest Recycler Price'));
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
                                    $productsExchangePrice[$product_id][] = ((float)$val['price'])/((float)$row->exchange_rate);
                                    $productsExchangeRate[$product_id][] = $row->exchange_rate;
                                    $productsExchangeDate[$product_id][] = $row->time;
                                }
                            }else{
                                $currentExchange = $exchangeTable->getCurrentExchangeOfCurrency($val['currency'],$startTime);
                                if(!empty($currentExchange)){
                                    $productsExchangePrice[$product_id][] = ((float)$val['price'])/((float)$currentExchange->exchange_rate);
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
                    $header = array($this->__('Date'),
                        $this->__('Model Name'),
                        $this->__('Condition'),
                        $this->__('Recycler Detail'),
                        $this->__('Price'),
                        $this->__('Currency'),
                        $this->__('Exchange Rate'),
                        $this->__('Price in HKD'),
                        $this->__('Highest Recycler Price'));
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
            echo '<option value="0">'.$this->__('Select Country').'</option>';
            exit();
        }
        $reyclerTable = $this->getServiceLocator()->get('RecyclerTable');
        $rowset = $reyclerTable->getRecyclersByCountry($country);
        $html = '<option value="0">'.$this->__('Select Country').'</option>';
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
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__('Please set start time'));
                return $this->redirect()->toUrl('/product/popular');
            }
            if(empty($post['products']) || $post['products'] == ''){
                $this->flashMessenger()->setNamespace('error')->addMessage($this->__('Please select products'));
                return $this->redirect()->toUrl('/product/popular');
            }
            $data = $post;
            $cache->removeItem('popular');
            $cache->addItem('popular',$data);
            $this->flashMessenger()->setNamespace('success')->addMessage($this->__('Update popular products success'));
            return $this->redirect()->toUrl('/product/popular');
        }
        $view = new ViewModel();
        $popular = $cache->getItem('popular');
        $view->setVariable('popular',$popular);
        return $view;
    }
}
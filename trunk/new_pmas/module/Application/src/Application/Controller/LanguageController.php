<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/23/13
 */
namespace Application\Controller;

use Application\Form\LanguageForm;
use Core\Cache\CacheSerializer;
use Core\Controller\AbstractController;
use Core\Model\Languages;
use Zend\Debug\Debug;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;

class LanguageController extends AbstractController
{
    public function indexAction()
    {
        parent::initAction();
        /*$data = $this->getViewHelperPlugin('lang')->readTranslateData('/upload/language/vn.csv');
        $this->getViewHelperPlugin('lang')->cachingTranslateData('vn',$data);
        $cache = CacheSerializer::init();
        $dataCached = $cache->getItem('vn-lang');
        $this->getViewHelperPlugin('lang')->removeCachedTranslateData('vn');
        $dataCached2 = $cache->getItem('vn-lang');
        Debug::dump($dataCached);
        Debug::dump($dataCached2);die;*/
        $languagesTable = $this->sm->get('LanguagesTable');
        $rowset = $languagesTable->getAvaiableRows();
        $this->setViewVariable('rowset',$rowset);
        return $this->view;
    }
    public function addAction()
    {
        parent::initAction();
        $request = $this->getRequest();
        $languagesTable = $this->sm->get('LanguagesTable');
        $messages = $this->getMessages();
        $form = new LanguageForm($this->sm);
        if($request->isPost()){
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['lang_country'])){
                $this->getViewHelperPlugin('user')->log('application\\language\\add',$messages['LOG_INSERT_LANGUAGE_FAIL'].' : '.$messages['COUNTRY_NAME_NOT_EMPTY']);
                $this->setViewVariable('msg',array('danger' => $messages['COUNTRY_NAME_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if(!$empty->isValid($post['lang_code'])){
                $this->getViewHelperPlugin('user')->log('application\\language\\add',$messages['LOG_INSERT_LANGUAGE_FAIL'].' : '.$messages['LANGUAGE_CODE_NOT_EMPTY']);
                $this->setViewVariable('msg',array('danger' => $messages['LANGUAGE_CODE_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $existe_lang = new NoRecordExists(array('table' => 'languages','field' => 'lang_code','adapter' => $dbAdapter));
            if(!$existe_lang->isValid($post['lang_code'])){
                $this->getViewHelperPlugin('user')->log('application\\language\\add',$messages['LOG_INSERT_LANGUAGE_FAIL'].' : '.$messages['LANGUAGE_CODE_EXISTED']);
                $this->setViewVariable('msg',array('danger' => $messages['LANGUAGE_CODE_EXISTED']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if(!$empty->isValid($post['sort_order'])){
                $this->getViewHelperPlugin('user')->log('application\\language\\add',$messages['LOG_INSERT_LANGUAGE_FAIL'].' : '.$messages['SORT_ORDER_NOT_EMPTY']);
                $this->setViewVariable('msg',array('danger' => $messages['SORT_ORDER_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if(!$post['file_upload']['name'] || $post['file_upload']['name'] == ''){
                $this->getViewHelperPlugin('user')->log('application\\language\\add',$messages['LOG_INSERT_LANGUAGE_FAIL'].' : '.$messages['LANG_FILE_NOT_UPLOAD']);
                $this->setViewVariable('msg',array('danger' => $messages['LANG_FILE_NOT_UPLOAD']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirectUrl('/language');
                }
                if($this->saveLanguage($data)){
                    /**
                     * Init cache languae
                     */
                    $id = $languagesTable->getLastInsertValue();
                    $this->getViewHelperPlugin('user')->log('application\\language\\add',$messages['LOG_INSERT_LANGUAGE_SUCCESS'].$id);
                    $this->addSuccessFlashMessenger($messages['INSERT_SUCCESS']);
                    if($continue == 'yes'){

                        return $this->redirectUrl('/language/edit/id/'.$id);
                    }else{
                        return $this->redirect()->toUrl('/language');
                    }
                }else{
                    $this->getViewHelperPlugin('user')->log('application\\language\\add',$messages['LOG_INSERT_LANGUAGE_FAIL']);
                    $this->setViewVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                    $this->setViewVariable('form',$form);
                    return $this->view;
                }
            }else{
                $this->getViewHelperPlugin('user')->log('application\\language\\add',$messages['LOG_INSERT_LANGUAGE_FAIL']);
                $this->setViewVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
        }
        $this->setViewVariable('form',$form);
        return $this->view;
    }
    public function editAction()
    {
        parent::initAction();
        $request = $this->getRequest();
        $id = $this->params('id',0);
        if(!$id || $id == 0){
            $this->getResponse()->setStatusCode(404);
        }
        $languagesTable = $this->sm->get('LanguagesTable');
        $entry = $languagesTable->getEntry($id);
        if(empty($entry)){
            $this->getResponse()->setStatusCode(404);
        }
        $code_before= $entry->lang_code;
        $this->setViewVariable('id',$id);
        $messages = $this->getMessages();
        $form = new LanguageForm($this->sm);
        if($request->isPost()){
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $continue = $post['continue'];
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['lang_country'])){
                $this->getViewHelperPlugin('user')->log('application\\language\\edit',$messages['LOG_UPDATE_LANGUAGE_FAIL'].$id.' : '.$messages['COUNTRY_NAME_NOT_EMPTY']);
                $this->setViewVariable('msg',array('danger' => $messages['COUNTRY_NAME_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if(!$empty->isValid($post['lang_code'])){
                $this->getViewHelperPlugin('user')->log('application\\language\\edit',$messages['LOG_UPDATE_LANGUAGE_FAIL'].$id.' : '.$messages['LANGUAGE_CODE_NOT_EMPTY']);
                $this->setViewVariable('msg',array('danger' => $messages['LANGUAGE_CODE_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $existe_lang = new NoRecordExists(array('table' => 'languages','field' => 'lang_code','adapter' => $dbAdapter,'exclude' => array('field' => 'lang_code','value' => $entry->lang_code)));
            if(!$existe_lang->isValid($post['lang_code'])){
                $this->getViewHelperPlugin('user')->log('application\\language\\edit',$messages['LOG_UPDATE_LANGUAGE_FAIL'].$id.' : '.$messages['LANGUAGE_CODE_EXISTED']);
                $this->setViewVariable('msg',array('danger' => $messages['LANGUAGE_CODE_EXISTED']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if(!$empty->isValid($post['sort_order'])){
                $this->getViewHelperPlugin('user')->log('application\\language\\edit',$messages['LOG_UPDATE_LANGUAGE_FAIL'].$id.' : '.$messages['SORT_ORDER_NOT_EMPTY']);
                $this->setViewVariable('msg',array('danger' => $messages['SORT_ORDER_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirectUrl('/language');
                }
                if($this->saveLanguage($data)){
                    /**
                     * Update Lang session
                     */
                    $this->getViewHelperPlugin('lang')->updateLanguageSession($id,$code_before);
                    $this->getViewHelperPlugin('user')->log('application\\language\\edit',$messages['LOG_UPDATE_LANGUAGE_SUCCESS'].$id);
                    $this->addSuccessFlashMessenger($messages['UPDATE_SUCCESS']);
                    if($continue == 'yes'){
                        return $this->redirectUrl('/language/edit/id/'.$id);
                    }else{
                        return $this->redirect()->toUrl('/language');
                    }
                }else{
                    $this->getViewHelperPlugin('user')->log('application\\language\\edit',$messages['LOG_UPDATE_LANGUAGE_FAIL'].$id);
                    $this->setViewVariable('msg',array('danger' => $messages['UPDATE_FAIL']));
                    $this->setViewVariable('form',$form);
                    return $this->view;
                }
            }else{
                $this->getViewHelperPlugin('user')->log('application\\language\\edit',$messages['LOG_UPDATE_LANGUAGE_FAIL'].$id);
                $this->setViewVariable('msg',array('danger' => $messages['UPDATE_FAIL']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
        }else{
            $populate = (array) $entry;
            $form->setData($populate);
        }
        $this->setViewVariable('form',$form);
        return $this->view;
    }
    public function deleteAction()
    {
        parent::initAction();
        $id = $this->params('id',0);
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $languagesTable = $this->sm->get('LanguagesTable');
        if($id != 0){
            $this->deleteLang($id,$languagesTable);
        }else{
            if(!empty($ids) && is_array($ids)){
                foreach($ids as $id){
                    $this->deleteUser($id,$languagesTable);
                }
            }
        }
        return $this->redirect()->toUrl('/lang');
    }
    protected function deleteLang($id,$table)
    {
        $messages = $this->getMessages();
        if($table->deleteEntry($id)){
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\language\\delete',$messages['LOG_DELETE_LANGUAGE_SUCCESS'].$id);
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\language\\delete',$messages['LOG_DELETE_LANGUAGE_FAIL'].$id);
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
        return true;
    }
    protected function saveLanguage($data)
    {
        $sm = $this->getServiceLocator();
        $languagesTable = $sm->get('LanguagesTable');
        $path = $this->getViewHelperPlugin('Files')->getUploadLanguageFolderPath();
        $file_url = $this->getViewHelperPlugin('Files')->getUploadLanguageFolderUrl();
        $dataFinal = $data;
        if($data['file_upload']['name'] != '' && trim($data['file_upload']['name']) != ''){
            $filename = $this->getViewHelperPlugin('files')->getFilenameUnique($data['file_upload']['name'],$path);
            move_uploaded_file($data['file_upload']['tmp_name'], $path . $filename);
            $dataFinal['file_path'] = '/upload/language/' .$filename;
        }
        $languages = new Languages();
        $languages->exchangeArray($dataFinal);
        $result = $languagesTable->save($languages);
        if($result){
            /**
             * Caching translate data
             */
            $data = $this->getViewHelperPlugin('lang')->readTranslateData($dataFinal['file_path']);
            $this->getViewHelperPlugin('lang')->cachingTranslateData($dataFinal['lang_code'],$data);
        }
        return $result;
    }
    public function changeAction()
    {
        parent::initAction();
        $code = $this->params('lang',null);
        $request = $this->getRequest();
        $referer = $request->getQuery('referer');
        if($code == null){
            exit();
        }
        $this->getViewHelperPlugin('lang')->setLanguageSession($code);
        $this->redirectUrl($referer);
    }
}
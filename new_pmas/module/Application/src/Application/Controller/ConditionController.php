<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/24/13
 */
namespace Application\Controller;

use Application\Form\ConditionForm;
use Core\Model\RecyclerProductCondition;
use Core\Model\TdmProductCondition;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;

class ConditionController extends AbstractActionController
{
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
        return $view;
    }
    public function tdmAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $tdmConditionTable = $this->getServiceLocator()->get('TdmProductConditionTable');
        $conditions = $tdmConditionTable->getAvaiableRows();
        $view = new ViewModel();
        $form = new ConditionForm($this->getServiceLocator());
        $view->setVariable('conditions',$conditions);
        $view->setVariable('form',$form);
        $id = $this->params('id',0);
        if($id != 0){
            $conditionEntry = $tdmConditionTable->getEntry($id);
            if(empty($conditionEntry)){
                $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                return $this->redirect()->toUrl('/condition/tdm');
            }
            $view->setVariable('name',$conditionEntry->name);
            $entryParse = (array) $conditionEntry;
            $form->setData($entryParse);
        }
        $view->setVariable('id',$id);
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['CONDITION_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check condition exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            if($id != 0){
                $exist_valid = new NoRecordExists(array('table' => 'tdm_product_condition','field' => 'name','adapter' => $dbAdapter,'exclude' => array('field' => 'name','value' => $conditionEntry->name)));
            }else{
                $exist_valid = new NoRecordExists(array('table' => 'tdm_product_condition','field' => 'name','adapter' => $dbAdapter));
            }
            if(!$exist_valid->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['CONDITION_NAME_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/condition/tdm');
                }
                if($this->saveTdmCondition($data)){
                    if($id != 0){
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPDATE_SUCCESS']);
                    }else{
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    }
                }else{
                    if($id != 0){
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['UPDATE_FAIL']);
                    }else{
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['INSERT_FAIL']);
                    }
                }
                return $this->redirect()->toUrl('/condition/tdm');
            }
        }
        return $view;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function saveTdmCondition($data)
    {
        $sm = $this->getServiceLocator();
        $conditionTable = $sm->get('TdmProductConditionTable');
        $dataFinal = $data;
        $condition = new TdmProductCondition();
        $condition->exchangeArray($dataFinal);
        return $conditionTable->save($condition);
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function saveRecyclerCondition($data)
    {
        $sm = $this->getServiceLocator();
        $conditionTable = $sm->get('RecyclerProductConditionTable');
        $dataFinal = $data;
        $condition = new RecyclerProductCondition();
        $condition->exchangeArray($dataFinal);
        return $conditionTable->save($condition);
    }
    public function deleteTdmAction()
    {
        $this->auth();
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $id = $this->params('id',0);
        $conditionTable = $this->getServiceLocator()->get('TdmProductConditionTable');
        if($id != 0){
            $this->deleteTdm($id,$conditionTable);
        }
        if(!empty($ids) && is_array($ids)){
            foreach($ids as $id){
                $this->deleteTdm($id,$conditionTable);
            }
        }
        return $this->redirect()->toUrl('/condition/tdm');
    }
    public function deleteRecyclerAction()
    {
        $this->auth();
        $request = $this->getRequest();
        $ids = $request->getPost('ids');
        $id = $this->params('id',0);
        $conditionTable = $this->getServiceLocator()->get('RecyclerProductConditionTable');
        if($id != 0){
            $this->deleteRecycler($id,$conditionTable);
        }
        if(!empty($ids) && is_array($ids)){
            foreach($ids as $id){
                $this->deleteRecycler($id,$conditionTable);
            }
        }
        return $this->redirect()->toUrl('/condition/recycler');
    }
    /**
     * Clear condition id
     * @param $id
     * @param $table
     * @return bool
     */
    protected function deleteTdm($id,$table)
    {
        $messages = $this->getMessages();
        $result = $table->clearTdmCondition($id);
        if($result){
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
        return true;
    }
    protected function deleteRecycler($id,$table)
    {
        $messages = $this->getMessages();
        $result = $table->clearRecyclerCondition($id);
        if($result){
            $this->flashMessenger()->setNamespace('success')->addMessage($messages['DELETE_SUCCESS']);
        }else{
            $this->flashMessenger()->setNamespace('error')->addMessage($messages['DELETE_FAIL']);
        }
        return true;
    }
    public function recyclerAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $recyclerConditionTable = $this->getServiceLocator()->get('RecyclerProductConditionTable');
        $conditions = $recyclerConditionTable->getAvaiableRows();
        $view = new ViewModel();
        $form = new ConditionForm($this->getServiceLocator());
        $view->setVariable('conditions',$conditions);
        $view->setVariable('form',$form);
        $id = $this->params('id',0);
        if($id != 0){
            $conditionEntry = $recyclerConditionTable->getEntry($id);
            if(empty($conditionEntry)){
                $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                return $this->redirect()->toUrl('/condition/recycler');
            }
            $view->setVariable('name',$conditionEntry->name);
            $entryParse = (array) $conditionEntry;
            $form->setData($entryParse);
        }
        $view->setVariable('id',$id);
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['CONDITION_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check condition exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            if($id != 0){
                $exist_valid = new NoRecordExists(array('table' => 'recycler_product_condition','field' => 'name','adapter' => $dbAdapter,'exclude' => array('field' => 'name','value' => $conditionEntry->name)));
            }else{
                $exist_valid = new NoRecordExists(array('table' => 'recycler_product_condition','field' => 'name','adapter' => $dbAdapter));
            }
            if(!$exist_valid->isValid($post['name'])){
                $view->setVariable('msg',array('danger' => $messages['CONDITION_NAME_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/condition/recycler');
                }
                if($this->saveRecyclerCondition($data)){
                    if($id != 0){
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['UPDATE_SUCCESS']);
                    }else{
                        $this->flashMessenger()->setNamespace('success')->addMessage($messages['INSERT_SUCCESS']);
                    }
                }else{
                    if($id != 0){
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['UPDATE_FAIL']);
                    }else{
                        $this->flashMessenger()->setNamespace('error')->addMessage($messages['INSERT_FAIL']);
                    }
                }
                return $this->redirect()->toUrl('/condition/recycler');
            }
        }
        return $view;
    }
}
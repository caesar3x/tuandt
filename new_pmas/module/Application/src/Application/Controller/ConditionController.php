<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/24/13
 */
namespace Application\Controller;

use Application\Form\ConditionForm;
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
    public function saveTdmCondition($data)
    {
        $sm = $this->getServiceLocator();
        $coutryTable = $sm->get('TdmProductConditionTable');
        $dataFinal = $data;
        $condition = new TdmProductCondition();
        $condition->exchangeArray($dataFinal);
        return $coutryTable->save($condition);
    }
}
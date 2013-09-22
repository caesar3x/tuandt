<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/14/13
 */
namespace Application\Controller;

use Application\Form\CountryForm;
use Core\Model\Country;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;

class ExchangeController extends AbstractActionController
{
    protected $exchangeTable;

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
    public function updateAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $view = new ViewModel();
        return $view;
    }
    public function countryAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $countryTable = $this->getServiceLocator()->get('CountryTable');
        $availableCountries = $countryTable->getAvaiableRows();
        $view = new ViewModel();
        $view->setVariable('countries',$availableCountries);
        $form = new CountryForm($this->getServiceLocator());
        $view->setVariable('form',$form);
        $request = $this->getRequest();
        $id = $this->params('id',0);
        $sm = $this->getServiceLocator();
        $coutryTable = $sm->get('CountryTable');
        if($id != 0){
            $countryEntry = $coutryTable->getEntry($id);
            if(empty($countryEntry)){
                $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                return $this->redirect()->toUrl('/exchange/country');
            }
            $entryParse = (array) $countryEntry;
            $entryParse['country_name'] = $countryEntry->name;
            $form->setData($entryParse);
        }

        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['country_name'])){
                $view->setVariable('msg',array('danger' => $messages['COUNTRY_NAME_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            if(!$empty->isValid($post['currency'])){
                $view->setVariable('msg',array('danger' => $messages['CURRENCY_NOT_EMPTY']));
                $view->setVariable('form',$form);
                return $view;
            }
            /**
             * check country exist
             */
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            if($id != 0){
                $exist_valid = new NoRecordExists(array('table' => 'country','field' => 'name','adapter' => $dbAdapter,'exclude' => array('field' => 'name','value' => $countryEntry->name)));
            }else{
                $exist_valid = new NoRecordExists(array('table' => 'country','field' => 'name','adapter' => $dbAdapter));
            }
            if(!$exist_valid->isValid($post['country_name'])){
                $view->setVariable('msg',array('danger' => $messages['COUNTRY_NAME_EXIST']));
                $view->setVariable('form',$form);
                return $view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->flashMessenger()->setNamespace('error')->addMessage($messages['NO_DATA']);
                    return $this->redirect()->toUrl('/exchange/country');
                }
                if($this->saveCountry($data)){
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
                if($id != 0){
                    return $this->redirect()->toUrl('/exchange/country/id/'.$id);
                }else{
                    return $this->redirect()->toUrl('/exchange/country');
                }
            }
        }
        return $view;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function saveCountry($data)
    {
        $sm = $this->getServiceLocator();
        $coutryTable = $sm->get('CountryTable');
        $dataFinal = $data;
        $dataFinal['name'] = $data['country_name'];
        $country = new Country();
        $country->exchangeArray($dataFinal);
        return $coutryTable->save($country);
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/27/13
 */
namespace Application\Controller;

use Application\Form\CountryForm;
use Core\Controller\AbstractController;
use Core\Model\Country;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;

class CountryController extends AbstractController
{
    public function indexAction()
    {
        parent::initAction();
        $messages = $this->getMessages();
        $countryTable = $this->sm->get('CountryTable');
        $availableCountries = $countryTable->getAvaiableRows();
        $this->setViewVariable('countries',$availableCountries);
        $form = new CountryForm($this->sm);
        $this->setViewVariable('form',$form);
        $request = $this->getRequest();
        $id = $this->params('id',0);
        if($id != 0){
            $countryEntry = $countryTable->getEntry($id);
            if(empty($countryEntry)){
                $this->addErrorFlashMessenger($messages['NO_DATA']);
                return $this->redirectUrl('/exchange/country');
            }
            $entryParse = (array) $countryEntry;
            $this->setViewVariable('name',$countryEntry->name);
            $entryParse['country_name'] = $countryEntry->name;
            $form->setData($entryParse);
        }
        $this->setViewVariable('id',$id);
        if($request->isPost()){
            $post = $request->getPost()->toArray();
            $form->setData($post);
            /**
             * Check empty
             */
            $empty = new NotEmpty();
            if(!$empty->isValid($post['country_name'])){
                $this->setViewVariable('msg',array('danger' => $messages['COUNTRY_NAME_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if(!$empty->isValid($post['currency'])){
                $this->setViewVariable('msg',array('danger' => $messages['CURRENCY_NOT_EMPTY']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            /**
             * check country exist
             */
            $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
            if($id != 0){
                $exist_valid = new NoRecordExists(array('table' => 'country','field' => 'name','adapter' => $dbAdapter,'exclude' => array('field' => 'name','value' => $countryEntry->name)));
            }else{
                $exist_valid = new NoRecordExists(array('table' => 'country','field' => 'name','adapter' => $dbAdapter));
            }
            if(!$exist_valid->isValid($post['country_name'])){
                $this->setViewVariable('msg',array('danger' => $messages['COUNTRY_NAME_EXIST']));
                $this->setViewVariable('form',$form);
                return $this->view;
            }
            if($form->isValid()){
                $data = $form->getData();
                if(empty($data)){
                    $this->addErrorFlashMessenger($messages['NO_DATA']);
                    return $this->redirectUrl('/exchange/country');
                }
                if($this->saveCountry($data)){
                    if($id != 0){
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\country\\index',$messages['LOG_UPDATE_COUNTRY_SUCCESS'].$id);
                        $this->addSuccessFlashMessenger($messages['UPDATE_SUCCESS']);
                    }else{
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\country\\index',$messages['LOG_INSERT_COUNTRY_SUCCESS'].$countryTable->getLastInsertValue());
                        $this->addSuccessFlashMessenger($messages['INSERT_SUCCESS']);
                    }
                }else{
                    if($id != 0){
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\country\\index',$messages['LOG_UPDATE_COUNTRY_FAIL'].$id);
                        $this->addErrorFlashMessenger($messages['UPDATE_FAIL']);
                    }else{
                        $this->getServiceLocator()->get('viewhelpermanager')->get('user')->log('application\\country\\index',$messages['LOG_INSERT_COUNTRY_FAIL']);
                        $this->setViewVariable('msg',array('danger' => $messages['INSERT_FAIL']));
                        $this->setViewVariable('form',$form);
                        return $this->view;
                    }
                }
                if($id != 0){
                    return $this->redirectUrl('/exchange/country/id/'.$id);
                }else{
                    return $this->redirectUrl('/exchange/country/id/');
                }
            }else{
                foreach($form->getMessages() as $msg){
                    $this->setViewVariable('msg',array('danger' => $msg));
                }
                $this->setViewVariable('form',$form);
                return $this->view;
            }
        }
        return $this->view;
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
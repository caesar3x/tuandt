<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/13/13
 */
namespace Application\Controller;

use Core\Model\Device;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ModelController extends AbstractActionController
{
    protected $deviceTable;

    protected $tdmDeviceTable;

    protected $recyclerDeviceTable;

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
        $view = new ViewModel();
        return $view;
    }
    public function addTdmAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $view = new ViewModel();
        return $view;
    }
    public function save($data)
    {
        $sm = $this->getServiceLocator();
        if(!$this->deviceTable){
            $this->deviceTable = $sm->get('DeviceTable');
        }
        $success = true;
        $dataFinal = $data;
        $device = new Device();
        $device->exchangeArray($dataFinal);
        $id = $device->device_id;
        if($id != 0){
            $entry = $this->deviceTable->getEntry($id);
            if(array_diff((array)$device,(array)$entry) != null){
                $success = $success && $this->deviceTable->save($device);
            }
        }else{
            if($this->deviceTable->save($device)){
                $success = $success && true;
            }else{
                $success = $success && false;
            }
        }
        return $success;
    }
    public function addAction()
    {
        $this->auth();
        $messages = $this->getMessages();
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $view = new ViewModel();
        return $view;
    }
    public function detailAction()
    {
        $view = new ViewModel();
        return $view;
    }
}
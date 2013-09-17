<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ResourceController extends AbstractActionController
{
    protected $resourcesTable;

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
        if (!$this->resourcesTable) {
            $sm = $this->getServiceLocator();
            $this->resourcesTable = $sm->get('ResourcesTable');
            $rowset = $this->resourcesTable->getAvaiableResources();
            $view->setVariable('rowset',$rowset);
        }
        return $view;
    }
    public function addAction()
    {
        $this->auth();
    }
    public function editAction()
    {

    }
}
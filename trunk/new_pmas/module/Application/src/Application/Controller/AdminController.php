<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/12/13
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $pluginManager = $this->getServiceLocator()->get('viewHelperManager');
        $helper        = $pluginManager->get('HeadTitle');
        $helper->append('Dashboard');
        return $view;
    }
    public function addAction()
    {
        $view = new ViewModel();
        return $view;
    }
    public function usersAction()
    {
        $view = new ViewModel();
        $pluginManager = $this->getServiceLocator()->get('viewHelperManager');
        $helper        = $pluginManager->get('HeadTitle');
        $helper->append('Manage Users');
        return $view;
    }
}
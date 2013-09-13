<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/13/13
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ModelController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        return $view;
    }
    public function addAction()
    {
        $view = new ViewModel();
        return $view;
    }
    public function detailAction()
    {
        $view = new ViewModel();
        return $view;
    }
}
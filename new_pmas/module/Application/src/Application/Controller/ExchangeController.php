<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/14/13
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ExchangeController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        return $view;
    }
    public function updateAction()
    {
        $view = new ViewModel();
        return $view;
    }
}
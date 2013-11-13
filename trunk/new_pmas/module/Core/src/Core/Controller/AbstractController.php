<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/27/13
 */
namespace Core\Controller;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class AbstractController extends AbstractActionController
{
    protected $sessionForm;

    protected $messages;

    protected $view;

    protected $sm;

    protected $viewhelper;

    public function __construct()
    {
        $container = new Container('FormData');
        $this->sessionForm = $container;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getViewHelperPlugin($name)
    {
        return $this->getServiceLocator()->get('viewhelpermanager')->get($name);
    }
    /**
     * Get auth service
     * @return \Zend\Http\Response
     */
    protected function auth()
    {
        $sm = $this->getServiceLocator();
        $authService = $sm->get('auth_service');
        if (! $authService->hasIdentity()) {
            return $this->redirect()->toUrl('/admin/login');
        }
    }

    /**
     * @return array|object
     */
    protected function getMessages()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('messages');
    }

    /**
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected function errorFlashMessenger()
    {
        return $this->flashMessenger()->setNamespace('error');
    }

    /**
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected function successFlashMessenger()
    {
        return $this->flashMessenger()->setNamespace('success');
    }

    /**
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected function infoFlashMessenger()
    {
        return $this->flashMessenger()->setNamespace('info');
    }

    /**
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected function defaultFlashMessenger()
    {
        return $this->flashMessenger()->setNamespace('default');
    }
    /**
     * @param $message
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected function addErrorFlashMessenger($message)
    {
        return $this->errorFlashMessenger()->addMessage($message);
    }

    /**
     * @param $message
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected function addSuccessFlashMessenger($message)
    {
        return $this->successFlashMessenger()->addMessage($message);
    }

    /**
     * @param $message
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected function addDefaultFlashMessenger($message)
    {
        return $this->defaultFlashMessenger()->addMessage($message);
    }

    /**
     * @param $message
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected function addInfoFlashMessenger($message)
    {
        return $this->infoFlashMessenger()->addMessage($message);
    }
    /**
     * @param $url
     * @return \Zend\Http\Response
     */
    protected function redirectUrl($url)
    {
        return $this->redirect()->toUrl($url);
    }

    /**
     * @return ViewModel
     */
    protected function initAction()
    {
        $this->auth();
        $view = new ViewModel();
        $this->view = $view;
        $this->messages = $this->getMessages();
        $this->sm = $this->getServiceLocator();
        return $view;
    }

    /**
     * @param $formKey
     * @param $data
     * @return mixed
     */
    protected function setFormData($formKey,$data)
    {
        return $this->sessionForm->offsetSet($formKey,$data);
    }

    /**
     * @param $formKey
     * @return mixed
     */
    protected function unsetFormData($formKey)
    {
        return $this->sessionForm->offsetUnset($formKey);
    }

    /**
     * @param $formKey
     * @return mixed
     */
    protected function getFormData($formKey)
    {
        return $this->sessionForm->offsetGet($formKey);
    }

    /**
     * @return array|object
     */
    protected function getDbAdapter()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return $dbAdapter;
    }

    /**
     * @param $key
     * @param $value
     * @return ViewModel
     */
    protected function setViewVariable($key,$value)
    {
        if(!$this->view){
            $this->view = new ViewModel();
        }
        return $this->view->setVariable($key,$value);
    }

    /**
     * @param $key
     * @param $value
     */
    protected function setLayoutVariable($key,$value)
    {
        $this->layout()->setVariable($key,$value);
    }

    /**
     * Translate string
     * @param $string
     * @return mixed
     */
    public function __($string)
    {
        return $this->getViewHelperPlugin('__')->trans($string);
    }
    /**
     * @param $message
     */
    protected function log($message)
    {
        $writer = new Stream($this->getViewHelperPlugin('log')->systemLogPath());
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->info($message);
    }

    /**
     * @param $message
     */
    protected function log_debug($message)
    {
        $writer = new Stream($this->getViewHelperPlugin('log')->systemDebugPath());
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->debug($message);
    }
}
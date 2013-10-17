<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
use Core\Controller\AbstractController;
use Core\Model\CacheSerializer;
use Zend\Debug\Debug;
use Zend\ServiceManager\ServiceManager;
use Zend\Soap\AutoDiscover;
use Zend\Soap\Client;
use Zend\Soap\Server;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractController
{
    public function auth()
    {
        $sm = $this->getServiceLocator();
        $authService = $sm->get('auth_service');
        if (! $authService->hasIdentity()) {
            return $this->redirect()->toUrl('login');
        }
    }
    public function getMessages()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('messages');
    }
    public function __construct()
    {

    }
    public function indexAction()
    {

        $this->auth();
        $cache = CacheSerializer::init();
        $popular = $cache->getItem('popular');
        $view = new ViewModel();
        $view->setVariable('popular',$popular);
        return $view;
    }
    public function soapAction()
    {
        /*$this->layout('layout/empty');*/
        if (isset($_GET['wsdl'])) {
            header ("Content-Type:text/xml");
            $this->handleWSDL();
            return $this->getResponse();
        } else {
            $this->handleSOAP();
        }
        return $this->getResponse();
    }
    private function handleWSDL() {
        /*$api = $this->getServiceLocator()->get('SoapApi');*/
        $autodiscover = new AutoDiscover();
        $autodiscover->setClass('serviceApi')->setUri('http://pmas.local/index/soap');
        $autodiscover->handle();
    }
    private function handleSOAP() {
        $soap = new Server(null,array(
            'uri' => 'http://pmas.local/index/soap?wsdl')
        );
        $api = $this->getServiceLocator()->get('SoapApi');
        /*$soap->setClass('MyClass');*/
        $soap->setClass('serviceApi');
        $soap->handle();
    }
    public function testAction()
    {
        $client = new Client('http://pmas.local/index/soap?wsdl');
        $result1 = $client->method(10);
        Debug::dump($result1);die;
    }
    public function method1($inputParam) {
        return 'Hello World :'.$inputParam;
    }
}
class SoapApi
{
    public function method1($inputParam) {
        return 'Hello World';
    }
}
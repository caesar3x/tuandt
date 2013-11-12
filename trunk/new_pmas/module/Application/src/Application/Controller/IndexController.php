<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
use Core\Cache\CacheSerializer;
use Core\Controller\AbstractController;
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
        /*$translator = $this->getServiceLocator()->get('translator');
        Debug::dump($translator->)*/
        $this->auth();
        $cache = CacheSerializer::init();
        $popular = $cache->getItem('popular');
        $view = new ViewModel();
        $view->setVariable('popular',$popular);
        return $view;
    }
    public function testAction()
    {
        $cache = \Core\Cache\CacheSerializer::init();
        $cache->addItem('translate',array('3434','4343','4343'));
        die('-----------------');
    }
}
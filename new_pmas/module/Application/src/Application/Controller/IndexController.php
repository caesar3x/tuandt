<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use BasicExcel\Reader\Csv;
use BasicExcel\Writer\Xlsx;
use Core\Model\CacheSerializer;
use Core\Model\SimpleXLSX;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Router\Console\Simple;
use Zend\View\Model\ViewModel;
use SimpleExcel\SimpleExcel;

class IndexController extends AbstractActionController
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
        Debug::dump($popular);
        $view = new ViewModel();
        $view->setVariable('popular',$popular);
        return $view;
    }
}

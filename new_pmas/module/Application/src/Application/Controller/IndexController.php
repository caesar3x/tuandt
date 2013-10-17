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
        $rowset = $this->getViewHelperPlugin('product')->getProductsByCountry(5);
        if(!empty($rowset)){
            echo $rowset->count().'<br/>';
            foreach($rowset as $row){
                Debug::dump($row);
            }
        }
        die('==============');
        $this->auth();
        $cache = CacheSerializer::init();
        $popular = $cache->getItem('popular');
        $view = new ViewModel();
        $view->setVariable('popular',$popular);
        return $view;
    }
}

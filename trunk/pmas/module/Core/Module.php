<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/5/13
 */
namespace Core;

use Core\Model\AdminUser;
use Core\Model\AdminUserTable;
use Core\Model\Terms;
use Core\Model\TermsTable;
use Core\Model\TermTaxonomy;
use Core\Model\TermTaxonomyTable;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        /**
         * Start Acl
         */
        $this -> initAcl($e);
        $eventManager->attach('route', array($this, 'checkAcl'));
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'auth_service' => function ($sm) {
                    $authService = new AuthenticationService();
                    $storage = new Session('backend_auth');
                    $authService->setStorage($storage);
                    return $authService;
                },
                'AdminUserTable' =>  function($sm) {
                    $tableGateway = $sm->get('AdminUserTableGateway');
                    $table = new AdminUserTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'AdminUserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AdminUser());
                    return new TableGateway('admin_user', $dbAdapter, null, $resultSetPrototype);
                },
                'TermsTable' => function($sm) {
                    $tableGateway = $sm->get('TermsTableGateway');
                    $table = new TermsTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'TermsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Terms());
                    return new TableGateway('terms', $dbAdapter, null, $resultSetPrototype);
                },
                'TermTaxonomyTable' => function($sm) {
                    $tableGateway = $sm->get('TermTaxonomyTableGateway');
                    $table = new TermTaxonomyTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'TermTaxonomyTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TermTaxonomy());
                    return new TableGateway('term_taxonomy', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'tokenString' => 'Backend\View\Helper\TokenString'
            ),
            'factories' => array(
                'admin' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\Admin();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'username' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\Username();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'pageHeader' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\PageHeader();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'slugify' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\Slugify();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'tokenString' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\TokenString();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
            ),
        );
    }
    public function initAcl(MvcEvent $e) {

        $acl = new Acl();
        $roles = include __DIR__ . '/config/module.acl.roles.php';
        $allResources = array();
        foreach ($roles as $role => $resources) {

            $role = new GenericRole($role);
            $acl->addRole($role);

            $allResources = array_merge($resources, $allResources);

            /*adding resources*/
            foreach ($resources as $resource) {
                if(!$acl->hasResource($resource))
                    $acl->addResource(new GenericResource($resource));
            }
            /*adding restrictions*/
            foreach ($allResources as $resource) {
                $acl->allow($role, $resource);
            }
        }
        /*Debug::dump($acl->isAllowed('editor','admin'));
        Debug::dump($allResources);
        Debug::dump($acl->isAllowed('admin','home'));die;*/

        /*setting to view*/
        $e->getViewModel()->acl = $acl;

    }

    public function checkAcl(MvcEvent $e) {
        $routeMatch = $e->getRouteMatch();
        $moduleNamespaceExp = explode('\\',$routeMatch->getParam('controller','backend'));
        $moduleNamespace = strtolower($moduleNamespaceExp[0]);
        $controllerName = strtolower($moduleNamespaceExp[2]);
        $actionName = strtolower($routeMatch->getParam('action', 'not-found'));
        $resource = $moduleNamespace.'\\'.$controllerName.'\\'.$actionName;
        $authService = $e->getApplication()->getServiceManager()->get('auth_service');
        $userRole = 'guest';
        if($authService->hasIdentity()){
            $userRole = $authService->getIdentity()->role;
        }
        if (!$e->getViewModel()->acl->isAllowed($userRole, $resource)) {
            /*$vm = $e->getViewModel();
            $vm->setTemplate('error/404');*/
            $response = $e->getResponse();
            $response -> getHeaders() -> addHeaderLine('Location', $e -> getRequest() -> getBaseUrl() . '/404');
            $response -> setStatusCode(404);
        }
    }
}
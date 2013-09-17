<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/5/13
 */
namespace Core;

use Core\Model\AdminUser;
use Core\Model\AdminUserTable;
use Core\Model\Country;
use Core\Model\CountryTable;
use Core\Model\Recycler;
use Core\Model\RecyclerTable;
use Core\Model\Resources;
use Core\Model\ResourcesTable;
use Core\Model\Roles;
use Core\Model\RolesTable;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Debug\Debug;
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
                'messages' => function ($sm) {
                    return $this->getConfigMessage();
                },
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
                'RolesTable' => function($sm) {
                    $tableGateway = $sm->get('RolesTableGateway');
                    $table = new RolesTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'RolesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Roles());
                    return new TableGateway('roles', $dbAdapter, null, $resultSetPrototype);
                },
                'ResourcesTable' => function($sm) {
                    $tableGateway = $sm->get('ResourcesTableGateway');
                    $table = new ResourcesTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'ResourcesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Resources());
                    return new TableGateway('resources', $dbAdapter, null, $resultSetPrototype);
                },
                'CountryTable' => function($sm) {
                    $tableGateway = $sm->get('CountryTableGateway');
                    $table = new CountryTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'CountryTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Country());
                    return new TableGateway('country', $dbAdapter, null, $resultSetPrototype);
                },
                'RecyclerTable' => function($sm) {
                    $tableGateway = $sm->get('RecyclerTableGateway');
                    $table = new RecyclerTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'RecyclerTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Recycler());
                    return new TableGateway('recycler', $dbAdapter, null, $resultSetPrototype);
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
                'role' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\Role();
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
                'currentUrl' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\CurrentUrl($serviceLocator->get('Request'));
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
            ),
        );
    }
    public function initAcl(MvcEvent $e,$re) {

        $acl = new Acl();
        $acl->addResource(new GenericResource($re));
        /*$roles = include __DIR__ . '/config/module.acl.roles.php';*/
        $allResources = array();
        $rolesTable = $e->getApplication()->getServiceManager()->get('RolesTable');
        $resourcesTable = $e->getApplication()->getServiceManager()->get('ResourcesTable');
        $avaiableResources = $resourcesTable->getAvaiableResources();
        $resourcesData = array();
        foreach($avaiableResources as $r){
            $resourcesData[$r->resource_id] = $r->path;
        }
        $results = $rolesTable->fetchAll();
        $roles = array();
        foreach($results as $row){
            $resource_ids = unserialize($row->resource_ids);
            if(!empty($resource_ids)){
                foreach($resource_ids as $resource_id){
                    $roles[$row->role][] = $resourcesData[$resource_id];
                }
            }
        }
        foreach ($roles as $role => $resources) {

            $role = new GenericRole($role);
            $acl->addRole($role);

            /*$allResources = array_merge($resources, $allResources);*/

            /*adding resources*/
            foreach ($resources as $resource) {
                if(!$acl->hasResource($resource)){
                    $acl->addResource(new GenericResource($resource));
                }
                $acl->allow($role, $resource);
            }
            /*adding restrictions*/
            foreach ($resources as $resource) {
                $acl->allow($role, $resource);
            }
        }
        $acl->addRole('super_admin');
        $acl->allow('super_admin');
        /*setting to view*/
        $e->getViewModel()->acl = $acl;

    }

    public function checkAcl(MvcEvent $e) {
        $rolesTable = $e->getApplication()->getServiceManager()->get('RolesTable');
        $routeMatch = $e->getRouteMatch();
        /**
         * set params
         */
        $param = $routeMatch->getParam('params', null);
        if (!is_null($param)) {
            $params = explode('/', $param);
            if (count ($params)) {
                while($paramKey = current($params)) {
                    $paramValue = next($params);
                    if ($paramValue) {
                        $routeMatch->setParam($paramKey, $paramValue);
                    }
                }
            }
        }
        $moduleNamespaceExp = explode('\\',$routeMatch->getParam('controller','backend'));
        $moduleNamespace = strtolower($moduleNamespaceExp[0]);
        $controllerName = strtolower($moduleNamespaceExp[2]);
        $actionName = strtolower($routeMatch->getParam('action', 'not-found'));
        $resource = $moduleNamespace.'\\'.$controllerName.'\\'.$actionName;
        $this -> initAcl($e,$resource);
        $authService = $e->getApplication()->getServiceManager()->get('auth_service');
        $userRole = 'guest';
        if($authService->hasIdentity()){
            $userRoleId = (int) $authService->getIdentity()->role;
            if($rolesTable->getRoleNameById($userRoleId)){
                $userRole = $rolesTable->getRoleNameById($userRoleId);
            }
        }
        if (!$e->getViewModel()->acl->isAllowed($userRole, $resource)) {
            /*$vm = $e->getViewModel();
            $vm->setTemplate('error/404');*/
            $response = $e->getResponse();
            /*$response -> getHeaders() -> addHeaderLine('Location', $e -> getRequest() -> getBaseUrl() . '/404');*/
            $response -> setStatusCode(404);
        }
    }
    public function getDbRoles(MvcEvent $e){
        // I take it that your adapter is already configured
        $rolesTable = $e->getApplication()->getServiceManager()->get('RolesTable');
        $results = $rolesTable->fetchAll();
        // making the roles array
        $roles = array();
        foreach($results as $result){
            $roles[$result['user_role']][] = $result['resource'];
        }
        return $roles;
    }
    public function getConfigMessage()
    {
        return include __DIR__ . '/config/module.message.php';
    }
}
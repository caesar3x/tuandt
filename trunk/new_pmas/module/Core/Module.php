<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/5/13
 */
namespace Core;

use Core\Model\AdminUser;
use Core\Model\AdminUserTable;
use Core\Model\Brand;
use Core\Model\BrandTable;
use Core\Model\Country;
use Core\Model\CountryTable;
use Core\Model\Product;
use Core\Model\ProductCondition;
use Core\Model\ProductConditionTable;
use Core\Model\ProductTable;
use Core\Model\ProductType;
use Core\Model\ProductTypeTable;
use Core\Model\Exchange;
use Core\Model\ExchangeTable;
use Core\Model\Recycler;
use Core\Model\RecyclerProduct;
use Core\Model\RecyclerProductCondition;
use Core\Model\RecyclerProductConditionTable;
use Core\Model\RecyclerProductTable;
use Core\Model\RecyclerTable;
use Core\Model\Resources;
use Core\Model\ResourcesTable;
use Core\Model\Roles;
use Core\Model\RolesTable;
use Core\Model\TdmProduct;
use Core\Model\TdmProductCondition;
use Core\Model\TdmProductConditionTable;
use Core\Model\TdmProductTable;
use Core\Model\TmpProduct;
use Core\Model\TmpProductTable;
use Core\Model\Usermeta;
use Core\Model\UsermetaTable;
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
                    return include __DIR__ . '/config/module.message.php';
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
                'ProductTypeTable' => function($sm) {
                    $tableGateway = $sm->get('ProductTypeTableGateway');
                    $table = new ProductTypeTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'ProductTypeTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ProductType());
                    return new TableGateway('product_type', $dbAdapter, null, $resultSetPrototype);
                },
                'TdmProductConditionTable' => function($sm) {
                    $tableGateway = $sm->get('TdmProductConditionTableGateway');
                    $table = new TdmProductConditionTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'TdmProductConditionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TdmProductCondition());
                    return new TableGateway('tdm_product_condition', $dbAdapter, null, $resultSetPrototype);
                },
                'RecyclerProductConditionTable' => function($sm) {
                    $tableGateway = $sm->get('RecyclerProductConditionTableGateway');
                    $table = new RecyclerProductConditionTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'RecyclerProductConditionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RecyclerProductCondition());
                    return new TableGateway('recycler_product_condition', $dbAdapter, null, $resultSetPrototype);
                },
                'BrandTable' => function($sm) {
                    $tableGateway = $sm->get('BrandTableGateway');
                    $table = new BrandTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'BrandTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Brand());
                    return new TableGateway('brand', $dbAdapter, null, $resultSetPrototype);
                },
                'TdmProductTable' => function($sm) {
                    $tableGateway = $sm->get('TdmProductTableGateway');
                    $table = new TdmProductTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'TdmProductTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TdmProduct());
                    return new TableGateway('tdm_product', $dbAdapter, null, $resultSetPrototype);
                },
                'TmpProductTable' => function($sm) {
                    $tableGateway = $sm->get('TmpProductTableGateway');
                    $table = new TmpProductTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'TmpProductTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TmpProduct());
                    return new TableGateway('tmp_product', $dbAdapter, null, $resultSetPrototype);
                },
                'RecyclerProductTable' => function($sm) {
                    $tableGateway = $sm->get('RecyclerProductTableGateway');
                    $table = new RecyclerProductTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'RecyclerProductTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RecyclerProduct());
                    return new TableGateway('recycler_product', $dbAdapter, null, $resultSetPrototype);
                },
                'ExchangeRateTable' => function($sm) {
                    $tableGateway = $sm->get('ExchangeRateTableGateway');
                    $table = new ExchangeTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'ExchangeRateTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Exchange());
                    return new TableGateway('exchange', $dbAdapter, null, $resultSetPrototype);
                },
                'UsermetaTable' => function($sm) {
                    $tableGateway = $sm->get('UsermetaTableGateway');
                    $table = new UsermetaTable($tableGateway);
                    $table->setServiceLocator($sm);
                    return $table;
                },
                'UsermetaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Usermeta());
                    return new TableGateway('usermeta', $dbAdapter, null, $resultSetPrototype);
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
                    $viewHelper = new View\Helper\AdminHelper();
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
                    $viewHelper = new View\Helper\RoleHelper();
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
                'country' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\CountryHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'productType' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\ProductTypeHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'createPath' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\CreatePath();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'condition' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\ConditionHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'productBrand' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\ProductBrand();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'recyclerImported' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\RecyclerImportedHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'exchange' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\ExchangeHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'recycler' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\RecyclerHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'price' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\PriceHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'product' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\ProductHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'menu' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\MenuHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'resource' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\ResourceHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'user' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\UserHelper();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'getUrl' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\UrlHelper();
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
            $resourcesData[$r->group][] = $r->path;
        }
        $results = $rolesTable->getAvaiableRoles();
        $roles = array();
        foreach($results as $row){
            if(!empty($row->resource_ids) && $row->resource_ids != ''){
                $resource_groups = unserialize($row->resource_ids);
                if(!empty($resource_groups)){
                    foreach($resource_groups as $resource_group){
                        if(array_key_exists($resource_group,$resourcesData)){
                            foreach($resourcesData[$resource_group] as $resource_item){
                                $roles[$row->role][] = $resource_item;
                            }
                        }
                    }
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
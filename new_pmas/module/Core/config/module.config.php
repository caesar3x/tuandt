<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/5/13
 */
return array(
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'super_admin_navigation' => 'Core\Navigation\Service\SuperAdminNavigationFactory',
            'top_menu_navigation' => 'Core\Navigation\Service\TopMenuNavigationFactory'
        ),
    ),
    'navigation' => array(
        'super_admin_navigation' => array(
            array(
                'label' => 'Dashboard',
                'route' => 'admin',
                'controller' => 'index',
                'action' => 'index',
            ),
            array(
                'label' => 'Recycler',
                'route' => 'admin',
                'controller' => 'recycler',
                'action' => 'index',
            ),
            array(
                'label' => 'TDM Model',
                'route' => 'admin',
                'controller' => 'product',
                'action' => 'index',
            ),
            array(
                'label' => 'Exchange Rate',
                'route' => 'admin',
                'controller' => 'exchange',
                'action' => 'index',
                'pages' => array(
                    array(
                        'label' => 'Historical Date',
                        'route' => 'admin',
                        'controller' => 'exchange',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Update Exchange Rate',
                        'route' => 'admin',
                        'controller' => 'exchange',
                        'action' => 'update',
                    ),
                    array(
                        'label' => 'Manage Countries',
                        'route' => 'admin',
                        'controller' => 'exchange',
                        'action' => 'country',
                    ),
                )
            ),
            array(
                'label' => 'System',
                'route' => 'admin',
                'controller' => 'system',
                'action' => 'index',
                'pages' => array(
                    array(
                        'label' => 'Manage Users',
                        'route' => 'admin',
                        'controller' => 'user',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Manage Roles',
                        'route' => 'admin',
                        'controller' => 'role',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Manage Resources',
                        'route' => 'admin',
                        'controller' => 'resource',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'TDM Conditions',
                        'route' => 'admin',
                        'controller' => 'condition',
                        'action' => 'tdm',
                    ),
                    array(
                        'label' => 'Recycler Conditions',
                        'route' => 'admin',
                        'controller' => 'condition',
                        'action' => 'recycler',
                    ),
                    array(
                        'label' => 'Product Types',
                        'route' => 'admin',
                        'controller' => 'product',
                        'action' => 'type',
                    ),
                ),
            ),
            array(
                'label' => 'Help',
                'route' => 'admin',
                'controller' => 'help',
                'action' => 'index',
            ),
        ),
        'top_menu_navigation' => array(
            array(
                'label' => 'Dashboard',
                'route' => 'admin',
                'controller' => 'index',
                'action' => 'index',
            ),
            array(
                'label' => 'Recycler',
                'route' => 'admin',
                'controller' => 'recycler',
                'action' => 'index'
            ),
            array(
                'label' => 'TDM Model',
                'route' => 'admin',
                'controller' => 'product',
                'action' => 'index',
            ),
            array(
                'label' => 'Exchange Rate',
                'route' => 'admin',
                'controller' => 'exchange',
                'action' => 'index',
                'pages' => array(
                    array(
                        'label' => 'Historical Date',
                        'route' => 'admin',
                        'controller' => 'exchange',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Update Exchange Rate',
                        'route' => 'admin',
                        'controller' => 'exchange',
                        'action' => 'update',
                    ),
                    array(
                        'label' => 'Manage Countries',
                        'route' => 'admin',
                        'controller' => 'exchange',
                        'action' => 'country',
                    ),
                )
            ),
            array(
                'label' => 'System',
                'route' => 'admin',
                'controller' => 'system',
                'action' => 'index',
            ),
            array(
                'label' => 'Help',
                'route' => 'admin',
                'controller' => 'help',
                'action' => 'index',
            ),
        ),
    ),
);
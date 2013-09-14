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
            'secondary_navigation' => 'Core\Navigation\Service\SecondaryNavigationFactory',
            'top_menu_navigation' => 'Core\Navigation\Service\TopMenuNavigationFactory'
        ),
    ),
    'navigation' => array(
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
                'action' => 'index',
                'pages' => array(
                    array(
                        'label' => 'Manage Recycler',
                        'route' => 'admin',
                        'controller' => 'recycler',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Add Recycler',
                        'route' => 'admin',
                        'controller' => 'recycler',
                        'action' => 'add',
                    ),
                ),
            ),
            array(
                'label' => 'TDM Model',
                'route' => 'admin',
                'controller' => 'model',
                'action' => 'index',
                'pages' => array(
                    array(
                        'label' => 'Manage Model',
                        'route' => 'admin',
                        'controller' => 'model',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Add Model',
                        'route' => 'admin',
                        'controller' => 'model',
                        'action' => 'add',
                    ),
                ),
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
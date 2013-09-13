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
        'default' => array(
            array(
                'label' => 'Dashboard',
                'route' => 'admin',
                'controller' => 'index',
                'action' => 'index',
            ),
            array(
                'label' => 'Manage Users',
                'route' => 'admin',
                'controller' => 'admin',
                'action' => 'users',
                'pages' => array(
                    array(
                        'label' => 'Thêm quản trị',
                        'route' => 'admin',
                        'controller' => 'admin',
                        'action' => 'add',
                    ),
                    array(
                        'label' => 'Sửa quản trị',
                        'route' => 'admin',
                        'controller' => 'admin',
                        'action' => 'edit',
                    ),
                ),
            ),
            array(
                'label' => 'Nội dung',
                'route' => 'admin',
                'controller' => 'term',
                'action' => 'index',
                'pages' => array(
                    array(
                        'label' => 'Danh mục',
                        'route' => 'admin',
                        'controller' => 'term',
                        'action' => 'category',
                        'pages' => array(
                            array(
                                'label' => 'Thêm danh mục',
                                'route' => 'admin',
                                'controller' => 'term',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Sửa danh mục',
                                'route' => 'admin',
                                'controller' => 'term',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Tags',
                        'route' => 'admin',
                        'controller' => 'term',
                        'action' => 'tag',
                    ),
                ),
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
                        'action' => 'addd',
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
                'controller' => 'model',
                'action' => 'index',
            ),
            array(
                'label' => 'System',
                'route' => 'admin',
                'controller' => 'model',
                'action' => 'index',
            ),
            array(
                'label' => 'Help',
                'route' => 'admin',
                'controller' => 'model',
                'action' => 'index',
            ),
        ),
    ),
);
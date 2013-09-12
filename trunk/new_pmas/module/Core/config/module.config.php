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
            'secondary_navigation' => 'Core\Navigation\Service\SecondaryNavigationFactory'
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
                'label' => 'Quản trị',
                'route' => 'admin',
                'controller' => 'admin',
                'action' => 'index',
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
        'secondary_navigation' => array(
            array(
                'label' => 'Thông tin Secondary',
                'route' => 'admin',
                'controller' => 'index',
                'action' => 'index',
            ),
            array(
                'label' => 'Quản trị Secondary',
                'route' => 'admin',
                'controller' => 'admin',
                'action' => 'index',
                'pages' => array(
                    array(
                        'label' => 'Thêm quản trị Secondary',
                        'route' => 'admin',
                        'controller' => 'admin',
                        'action' => 'add',
                    ),
                    array(
                        'label' => 'Sửa quản trị Secondary',
                        'route' => 'admin',
                        'controller' => 'admin',
                        'action' => 'edit',
                    ),
                ),
            ),
            array(
                'label' => 'Nội dung Secondary',
                'route' => 'admin',
                'controller' => 'term',
                'action' => 'index',
                'pages' => array(
                    array(
                        'label' => 'Danh mục Secondary',
                        'route' => 'admin',
                        'controller' => 'term',
                        'action' => 'category',
                        'pages' => array(
                            array(
                                'label' => 'Thêm danh mục Secondary',
                                'route' => 'admin',
                                'controller' => 'term',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Sửa danh mục Secondary',
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
    ),
);
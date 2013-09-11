<?php
return array (
  'router' => 
  array (
    'routes' => 
    array (
      'home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/',
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Index',
            'action' => 'index',
          ),
        ),
      ),
      'application' => 
      array (
        'type' => 'Literal',
        'options' => 
        array (
          'route' => '/application',
          'defaults' => 
          array (
            '__NAMESPACE__' => 'Application\\Controller',
            'controller' => 'Index',
            'action' => 'index',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'default' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/[:controller[/:action]]',
              'constraints' => 
              array (
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
              ),
              'defaults' => 
              array (
              ),
            ),
          ),
        ),
      ),
      'admin' => 
      array (
        'type' => 'Literal',
        'options' => 
        array (
          'route' => '/admin',
          'defaults' => 
          array (
            '__NAMESPACE__' => 'Backend\\Controller',
            'controller' => 'Index',
            'action' => 'index',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'default' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/[:controller[/:action]]',
              'constraints' => 
              array (
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
              ),
              'defaults' => 
              array (
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  'service_manager' => 
  array (
    'abstract_factories' => 
    array (
      0 => 'Zend\\Cache\\Service\\StorageCacheAbstractServiceFactory',
      1 => 'Zend\\Log\\LoggerAbstractServiceFactory',
      2 => 'Zend\\Cache\\Service\\StorageCacheAbstractServiceFactory',
      3 => 'Zend\\Log\\LoggerAbstractServiceFactory',
    ),
    'aliases' => 
    array (
      'translator' => 'MvcTranslator',
    ),
    'factories' => 
    array (
      'Zend\\Db\\Adapter\\Adapter' => 
      Closure::__set_state(array(
      )),
    ),
  ),
  'translator' => 
  array (
    'locale' => 'en_US',
    'translation_file_patterns' => 
    array (
      0 => 
      array (
        'type' => 'gettext',
        'base_dir' => 'D:\\www\\zend\\chorong\\module\\Application\\config/../language',
        'pattern' => '%s.mo',
      ),
    ),
  ),
  'controllers' => 
  array (
    'invokables' => 
    array (
      'Application\\Controller\\Index' => 'Application\\Controller\\IndexController',
      'Backend\\Controller\\Index' => 'Backend\\Controller\\IndexController',
    ),
  ),
  'view_manager' => 
  array (
    'display_not_found_reason' => true,
    'display_exceptions' => true,
    'doctype' => 'HTML5',
    'not_found_template' => 'error/404',
    'exception_template' => 'error/index',
    'template_map' => 
    array (
      'layout/layout' => 'D:\\www\\zend\\chorong\\module\\Application\\config/../view/layout/layout.phtml',
      'application/index/index' => 'D:\\www\\zend\\chorong\\module\\Application\\config/../view/application/index/index.phtml',
      'error/404' => 'D:\\www\\zend\\chorong\\module\\Application\\config/../view/error/404.phtml',
      'error/index' => 'D:\\www\\zend\\chorong\\module\\Application\\config/../view/error/index.phtml',
      'backend/layout' => 'D:\\www\\zend\\chorong\\module\\Backend\\config/../view/layout/layout.phtml',
      'backend/login' => 'D:\\www\\zend\\chorong\\module\\Backend\\config/../view/layout/login.phtml',
      'backend/index/index' => 'D:\\www\\zend\\chorong\\module\\Backend\\config/../view/backend/index/index.phtml',
      'backend/index/login' => 'D:\\www\\zend\\chorong\\module\\Backend\\config/../view/backend/index/login.phtml',
    ),
    'template_path_stack' => 
    array (
      0 => 'D:\\www\\zend\\chorong\\module\\Application\\config/../view',
      1 => 'D:\\www\\zend\\chorong\\module\\Backend\\config/../view',
    ),
  ),
  'console' => 
  array (
    'router' => 
    array (
      'routes' => 
      array (
      ),
    ),
  ),
  'db' => 
  array (
    'driver' => 'Pdo',
    'dsn' => 'mysql:dbname=zf2tutorial;host=localhost',
  ),
);
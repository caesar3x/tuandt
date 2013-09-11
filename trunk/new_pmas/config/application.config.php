<?php
$env = getenv('APP_ENV') ?: 'production';
// Use the $env value to determine which modules to load
$modules = array(
    'Application',
    'Core'
);
if ($env == 'development') {
    $modules[] = 'ZendDeveloperTools';
}
return array(
    'modules' => $modules,
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
        ),
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),

        // Use the $env value to determine the state of the flag
        'config_cache_enabled' => false,
        //'config_cache_enabled' => ($env == 'production'),

        'config_cache_key' => 'app_config',

        // Use the $env value to determine the state of the flag
        'module_map_cache_enabled' => false,
        //'module_map_cache_enabled' => ($env == 'production'),

        'module_map_cache_key' => 'module_map',

        'cache_dir' => 'data/cache/',

        // Whether or not to enable modules dependency checking.
        // Enabled by default, prevents usage of modules that depend on other modules
        // that weren't loaded.
        'check_dependencies' => ($env != 'production'),
    ),

    // Used to create an own service manager. May contain one or more child arrays.
    //'service_listener_options' => array(
    //     array(
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ),
    // )

    // Initial configuration with which to seed the ServiceManager.
    // Should be compatible with Zend\ServiceManager\Config.
    // 'service_manager' => array(),
);

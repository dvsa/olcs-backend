<?php

return [
    // This should be an array of module namespaces used in the application.
    'modules' => [
        'Dvsa\LaminasConfigCloudParameters',
        'Laminas\Log',
        'Olcs\Logging',
        'Laminas\Router',
        'Laminas\Cache\Module',
        'Laminas\Cache\Storage\Adapter\Redis',
        'Laminas\Filter\Module',
        'Laminas\Validator\Module',
        'Laminas\Mvc\I18n\Module',
        'Laminas\I18n\Module',
        'Dvsa\Olcs\Utils',
        'Dvsa\Olcs\Auth',
        'Dvsa\Olcs\Snapshot',
        'Dvsa\Olcs\Email',
        'Dvsa\Olcs\CompaniesHouse',
        'Dvsa\Olcs\Cpms',
        'Dvsa\Olcs\DocumentShare',
        'Dvsa\Olcs\Db',
        'LmcRbacMvc',
        'Dvsa\Olcs\Transfer',
        'DoctrineModule',
        'DoctrineORMModule',
        'Dvsa\Olcs\AcquiredRights',
        'Dvsa\Olcs\Api',
        'Dvsa\Olcs\Scanning',
        'Soflomo\Purifier',
        'Olcs\XmlTools',
        'Dvsa\Olcs\GdsVerify',
        'Dvsa\Olcs\AwsSdk',
        'Dvsa\Olcs\Queue',
        'Dvsa\Olcs\DvsaAddressService',
        'Dvsa\Olcs\Address', // Load after DvsaAddressService as it may overwrite the AddressService alias until removed
    ],
    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => [
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => [
            __DIR__ . '/../module',
            __DIR__ . '/../vendor'
        ],

        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively override configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        'config_glob_paths' => [
            realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php',
        ],

        // Whether or not to enable a configuration cache.
        // If enabled, the merged configuration will be cached and used in
        // subsequent requests.
        'config_cache_enabled' => true,

        // The key used to create the configuration cache file name.
        'config_cache_key' => 'application.config.cache',

        // Whether or not to enable a module class map cache.
        // If enabled, creates a module class map cache which will be used
        // by in future requests, to reduce the autoloading process.
        'module_map_cache_enabled' => true,

        // The key used to create the class map cache file name.
        //'module_map_cache_key' => $stringKey,

        // The path in which to cache merged configuration.
        'cache_dir' => 'data/cache/',

        // Whether or not to enable modules dependency checking.
        // Enabled by default, prevents usage of modules that depend on other modules
        // that weren't loaded.
        // 'check_dependencies' => true,
    ],

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
   // Should be compatible with Laminas\ServiceManager\Config.
   // 'service_manager' => array(),
];

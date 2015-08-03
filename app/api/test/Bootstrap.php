<?php

namespace OlcsTest;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Mockery as m;

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Europe/London');
chdir(dirname(__DIR__));

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $config = array();

    public static function init()
    {
        ini_set('memory_limit', '1G');
        // Setup the autloader
        $loader = static::initAutoloader();

        $loader->addPsr4('OlcsTest\\Db\\', __DIR__ . '/module/Olcs/Db/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\Api\\', __DIR__ . '/module/Api/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\Cli\\', __DIR__ . '/module/Cli/src/');

        // Grab the application config
        $config = include dirname(__DIR__) . '/config/application.config.php';

        self::$config = $config;

        self::getServiceManager();
    }

    public static function getServiceManager()
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceManager')
            ->makePartial()
            ->setAllowOverride(true);

        return $sm;
    }

    protected static function initAutoloader()
    {
        require('init_autoloader.php');

        return $loader;
    }
}

Bootstrap::init();

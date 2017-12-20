<?php

namespace OlcsTest;

use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\ServiceLocatorInterface;
use Mockery as m;

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $config = array();

    public static function init()
    {
        date_default_timezone_set('UTC');

        ini_set('memory_limit', '1500M');
        // Setup the autloader
        $loader = static::initAutoloader();

        $loader->addPsr4('OlcsTest\\Db\\', __DIR__ . '/module/Olcs/Db/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\Api\\', __DIR__ . '/module/Api/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\Cli\\', __DIR__ . '/module/Cli/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\Snapshot\\', __DIR__ . '/module/Snapshot/src/');

        // Grab the application config
        $config = include dirname(__DIR__) . '/config/application.config.php';

        self::$config = $config;

        self::setupLogger();
    }

    public static function setupLogger()
    {
        $logWriter = new \Zend\Log\Writer\Mock();
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($logWriter);

        Logger::setLogger($logger);
    }

    public static function getServiceManager()
    {
        $sm = m::mock(ServiceLocatorInterface::class);

        $sm->shouldReceive('setService')
            ->andReturnUsing(
                function ($alias, $service) use ($sm) {
                    $sm->shouldReceive('get')->with($alias)->andReturn($service);
                    $sm->shouldReceive('has')->with($alias)->andReturn(true);
                    return $sm;
                }
            );

        return $sm;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected static function initAutoloader()
    {
        require('init_autoloader.php');

        return $loader;
    }
}

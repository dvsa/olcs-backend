<?php

namespace OlcsTest;

use Olcs\Logging\Log\Logger;
use Laminas\ServiceManager\ServiceLocatorInterface;
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

        ini_set('memory_limit', '4G');
        // Setup the autloader
        $loader = static::initAutoloader();

        $loader->addPsr4('OlcsTest\\Db\\', __DIR__ . '/module/Olcs/Db/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\Api\\', __DIR__ . '/module/Api/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\Cli\\', __DIR__ . '/module/Cli/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\Snapshot\\', __DIR__ . '/module/Snapshot/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\AwsSdk\\', __DIR__ . '/module/AwsSdk/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\Queue\\', __DIR__ . '/module/Queue/src/');
        $loader->addPsr4('Dvsa\\OlcsTest\\Builder\\', __DIR__ . '/src/Builder');

        // Grab the application config
        $config = include dirname(__DIR__) . '/config/application.config.php';

        self::$config = $config;

        self::setupLogger();
    }

    public static function setupLogger()
    {
        $logWriter = new \Laminas\Log\Writer\Mock();
        $logger = new \Laminas\Log\Logger();
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

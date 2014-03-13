<?php
namespace Olcs\Db\Test;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

class TestModuleLoader
{
    private $config;
    private $serviceManager;

    public function __construct($testConfig)
    {
        $this->autoload();
        $this->config = $this->correctConfig($testConfig);
        $this->serviceManager = $this->newServiceManager();
    }

    public function getServiceManager() {
        return $this->serviceManager;
    }

    public function getConfig() {
        return $this->config;
    }

    public function autoload() {
        include $this->findParentPath('vendor') . '/autoload.php';
    }

    public function newServiceManager() {
        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $this->config);
        $serviceManager->get('ModuleManager')->loadModules();

        return $serviceManager;
    }

    public function correctConfig($testConfig) {
        $zf2ModulePaths = array();

        $modulePaths = $testConfig['module_listener_options']['module_paths'];
        foreach ($modulePaths as $modulePath) {
            if (($path = static::findParentPath($modulePath)) ) {
                $zf2ModulePaths[] = $path;
            }
        }

        $testConfig['module_listener_options']['module_paths'] = $zf2ModulePaths;

        return $testConfig;
    }

    private function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}


class Bootstrap {
    protected static $serviceManager;
    protected static $config;

    public static function getServiceManager() {
        return static::$serviceManager;
    }

    public static function getConfig() {
        return static::$config;
    }

    public static function init() {
        $loader = new TestModuleLoader(include 'config/test.config.php');
        self::$config = $loader->getConfig();
        self::$serviceManager = $loader->getServiceManager();
    }
}

Bootstrap::init();
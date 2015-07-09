<?php
/**
 * Cli Module
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Cli;

use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapterInterface;
use Zend\Mvc\MvcEvent;

/**
 * Cli Module
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Module implements ConsoleUsageProviderInterface
{
    /**
     * Display CLI usage
     *
     * @param ConsoleAdapterInterface $console
     *
     * @return array
     */
    public function getConsoleUsage(ConsoleAdapterInterface $console)
    {
        // supress PMD error
        unset($console);

        return array(
            // Describe available commands
            'licence-status-rules [--verbose|-v]' => 'Process licence status change rules',
            'enqueue-ch-compare [--verbose|-v]' => 'Enqueue Companies House lookups for all Organisations',
            // @TODO remove
            'ch-initial-load [--verbose|-v]' => 'TEST action for Companies House api stuff',
            array( '--verbose|-v', '(optional) turn on verbose mode'),
        );
    }

    public function onBootstrap(MvcEvent $event)
    {
        // block session saving when running cli, as causes permissions errors
        if (PHP_SAPI === 'cli') {
            $handler = new Session\NullSaveHandler();
            $manager = new \Zend\Session\SessionManager();
            $manager->setSaveHandler($handler);
            \Zend\Session\Container::setDefaultManager($manager);
        }
    }

    public function getConfig()
    {
        $base = include __DIR__ . '/../config/module.config.php';
        return $base;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ ,
                ),
            ),
        );
    }
}

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
            'duplicate-vehicle-warning [--verbose|-v]' => 'Send duplicate vehicle warning letters',
            'process-inbox [--verbose|-v]' => 'Process inbox documents',
            'batch-cns  [--verbose|-v] [--dryrun|-d]' => 'Process Licences for Continuation Not Sought',
            'inspection-request-email [--verbose|-v]' => 'Process inspection request email',
            'remove-read-audit [--verbose|-v]' => 'Process deletion of old read audit records',
            // Describe parameters
            array( '--verbose|-v', '(optional) turn on verbose mode'),
            array( '--dryrun|-d', '(optional) dryrun, nothing is actually changed'),
            'process-queue [--type=]' => 'Process the queue',
            array( '--type=<que_typ_xxx>', '(optional) queue message type to process'),
        );
    }

    /**
     * @inheritdoc
     */
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

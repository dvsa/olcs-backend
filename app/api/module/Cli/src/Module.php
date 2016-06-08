<?php

namespace Dvsa\Olcs\Cli;

use Dvsa\Olcs\Cli\Domain\CommandHandler\DataGovUkExport;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapterInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
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

        return [
            // Describe available commands
            'licence-status-rules [--verbose|-v]' => 'Process licence status change rules',
            'enqueue-ch-compare [--verbose|-v]' => 'Enqueue Companies House lookups for all Organisations',
            'duplicate-vehicle-warning [--verbose|-v]' => 'Send duplicate vehicle warning letters',
            'process-inbox [--verbose|-v]' => 'Process inbox documents',
            'process-ntu [--verbose|-v]' => 'Process Not Taken Up Applications',
            'batch-cns  [--verbose|-v] [--dryrun|-d]' => 'Process Licences for Continuation Not Sought',
            'inspection-request-email [--verbose|-v]' => 'Process inspection request email',
            'remove-read-audit [--verbose|-v]' => 'Process deletion of old read audit records',
            'system-parameter name value [--verbose|-v]' => 'Set a system parameter',
            'resolve-payments [--verbose|-v]' => 'Resolve pending CPMS payments',
            'create-vi-extract-files [--verbose|-v] [--oc|-oc] [--op|-op] [--tnm|-tnm] [--vhl|-vhl] [--all|-all]' .
                ' [--path=<exportPath>] ' => 'Create mobile compliance VI extract files',
            // Describe parameters
            ['--verbose|-v', '(optional) turn on verbose mode'],
            ['--dryrun|-d', '(optional) dryrun, nothing is actually changed'],
            'process-queue [--type=]' => 'Process the queue',
            ['--type=<que_typ_xxx>', '(optional) queue message type to process'],

            'data-gov-uk-export <report-name> [--verbose|-v] [--path=<exportPath>]' => 'Export to csv for data.gov.uk',
            ['<report-name>', 'export report name'],
            ['    ' . DataGovUkExport::OPERATOR_LICENCE, '- export operator licences'],
            ['    ' . DataGovUkExport::BUS_REGISTERED_ONLY, '- export bus registered only'],
            ['    ' . DataGovUkExport::BUS_VARIATION, '- export bus variations'],
            ['--path=<exportPath>', '(optional) save export file in specified directory'],
        ];
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
        return (include __DIR__ . '/../config/module.config.php');
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

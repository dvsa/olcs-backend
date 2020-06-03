<?php

namespace Dvsa\Olcs\Cli;

use Dvsa\Olcs\Cli\Domain\CommandHandler\DataGovUkExport;
use Dvsa\Olcs\Cli\Domain\CommandHandler\DataDvaNiExport;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapterInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

/**
 * Cli Module
 *
 * @codeCoverageIgnore
 */
class Module implements ConsoleUsageProviderInterface
{
    /**
     * Display CLI usage
     *
     * @param ConsoleAdapterInterface $console console
     *
     * @return array
     */
    public function getConsoleUsage(ConsoleAdapterInterface $console)
    {
        // supress PMD error
        unset($console);
        return [
            // Describe available commands
            'expire-bus-registration [--verbose|-v]' => 'Expire bus registrations past their end date',
            'flag-urgent-tasks [--verbose|-v]' => 'Flag applicable tasks as urgent',
            'licence-status-rules [--verbose|-v]' => 'Process licence status change rules',
            'enqueue-ch-compare [--verbose|-v]' => 'Enqueue Companies House lookups for all Organisations',
            'duplicate-vehicle-warning [--verbose|-v]' => 'Send duplicate vehicle warning letters',
            'process-inbox [--verbose|-v]' => 'Process inbox documents',
            'process-ntu [--verbose|-v]' => 'Process Not Taken Up Applications',
            'process-cl [--verbose|-v] [--dryrun|-d]' => 'Process community licences',
            'batch-cns  [--verbose|-v] [--dryrun|-d]' => 'Process Licences for Continuation Not Sought',
            'batch-clean-variations  [--verbose|-v] [--dryrun|-d]' => 'Clean up abandoned variations',
            'inspection-request-email [--verbose|-v]' => 'Process inspection request email',
            'remove-read-audit [--verbose|-v]' => 'Process deletion of old read audit records',
            'system-parameter name value [--verbose|-v]' => 'Set a system parameter',
            'resolve-payments [--verbose|-v]' => 'Resolve pending CPMS payments',
            'create-vi-extract-files' => 'Create mobile compliance VI extract files',
            ['--oc|-oc', 'Export Operating Centres file'],
            ['--op|-op', 'Export Operators file'],
            ['--tnm|-tnm', 'Export Trading Names file'],
            ['--vhl|-vhl', 'Export Vehicle file'],
            ['--all|-all', 'Export all 4 files'],
            ['--path=<exportPath>', 'Path to create exported files'],
            ['--verbose|-v', '(optional) turn on verbose mode'],
            ['--dryrun|-d', '(optional) dryrun, nothing is actually changed'],
            'duplicate-vehicle-removal [--verbose|-v]' => 'Duplicate vehicle removal',
            'last-tm-letter [--verbose|-v]' => 'Generate and send Last TM Letter',
            'get-db-value [--property-name=] [--entity-name=] [--filter-property=] [--filter-value=] [--verbose|-v]' => 'Return a value from DB given the specified parameters',
            ['--property-name', 'Name of the property to retrieve the value for (e.g. "licNo")'],
            [
                '--entity-name',
                'Full Name of the Entity (e.g. for Licence entity "Licence\Licence", for BusReg entity "Bus\BusReg")'
            ],
            ['--filter-property', 'Name of the property to property to filter by (e.g. "id")'],
            ['--filter-value', 'Value to use for the filter-property'],
            'process-queue' => 'Process the queue',
            ['--type=<que_typ_xxx>', '(optional) queue message type to process can be a comma seperated list'],
            ['--exclude=<que_typ_xxx>', '(optional) DON\'t process message type, can be a comma seperated list'],
            ['--queue-duration=SECONDS', '(optional) Number of seconds the queue process will run for'],
            'data-gov-uk-export <report-name> [--verbose|-v] [--path=<exportPath>]' => 'Export to csv for data.gov.uk',
            ['<report-name>', 'Export report name'],
            ['    ' . DataGovUkExport::OPERATOR_LICENCE, 'Export operator licences'],
            ['    ' . DataGovUkExport::BUS_REGISTERED_ONLY, 'Export bus registered only'],
            ['    ' . DataGovUkExport::BUS_VARIATION, 'Export bus variations'],
            ['    ' . DataGovUkExport::PSV_OPERATOR_LIST, 'Export psv operator list and send attachment in email'],
            [
                '    ' . DataGovUkExport::INTERNATIONAL_GOODS,
                'Export standard international goods licences report to ' .
                'CSV and send by email'
            ],
            ['--path=<exportPath>', '(optional) save export file in specified directory'],
            //
            'data-dva-ni-export <report-name> [--verbose|-v] [--path=<exportPath>]' =>
                'Export to csv for Northern Ireland',
            ['<report-name>', 'Export report name'],
            ['    ' . DataDvaNiExport::NI_OPERATOR_LICENCE, 'Export GV operator licences for NI'],
            ['--path=<exportPath>', '(optional) save export file in specified directory'],
            //
            'ch-vs-olcs-diffs [--verbose|-v] [--path=<exportPath>]' =>
                'Compare data at olcs vs companies house and export to csv',
            ['--path=<exportPath>', '(optional) save export file in specified directory'],
            //
            'import-users-from-csv <csv-path> [--result-csv-path=<result-csv-path>] [--verbose|-v]' =>
                'Import user from csv file',
            ['<csv-path>', 'path to csv file with users for import'],
            [
                '--result-csv-path=<result-csv-path>',
                "(optional) save result to specified file.\n" .
                'By default, result will be saved to "<csv-path>-res.csv" file'
            ],
            'data-retention-rule <populate|delete> [--limit] [--verbose|-v]' =>
                'Run the data retention rules',
            [
                '<populate|delete>',
                'action to perform, ie \'populate\' the data to be deleted or \'delete\' previously populated data',
            ],
            [
                '--limit',
                'Number of data retention records to process (NB only applicable when deleting)'
            ],
            'dr-checks <precheck|postcheck>' =>
                'Run the DR pre or post checks',
            [
                '<precheck|postcheck>',
                '\'precheck\' can be run after the data-retention-rule populate has complete and before the delete phase. \'postcheck\' should be run once delete is complete.',
            ],
            'digital-continuation-reminders [--verbose|-v]' => 'Generate/Send checklists for digital continuations',
            'create-psv-licence-surrender-tasks [--verbose|-v] [--dryrun|-d]' =>
                'Create tasks to surrender PSV licences that have expired',
            'database-maintenance [--verbose|-v]' => 'Perform database management tasks, eg changing is_irfo flags',
            'identify-successful-permit-applications <stock-id> [--verbose|-v]' => 'Identify successful permit applications',
            'permits mark-expired-permits [--verbose|-v]' =>
                'Expire permits, permits applications, roadworthiness certificates',
            'permits close-expired-windows [--since=<date>] [--verbose|-v]' =>
                'Run permit action',
            'permits withdraw-unpaid [--verbose|-v]' =>
                'Withdraw applications and handle unpaid fees once payment deadline has passed',
            [
                '<close-expired-windows> [--since=<date>]',
                'action to perform, ie \'close-expired-windows\' to close all recently expired windows',
            ],

            'populate-last-login --limit=<limit> --batch-size=<batchSize> [--live]  [--show-progress] [--verbose|-v]' =>
                'Populate last login timestamp in user table with data from OpenAM',
                [
                    '--live',
                    'Explicit flag to indicate this is not a dry run',
                ],
                [
                    '--batch-size=<batchSize>',
                    'Defines the number of users to be queried from openAM and updated in the database in one go'
                ],
                [
                    '--limit=<limit>',
                    'Limit the number of users to process. Set it to -1 to process all users.'
                ],
                [
                    '--show-progress',
                    'Show a progress bar'
                ],

            'poll-sqs <queue> [--queue-duration=<seconds>] [--verbose|-v]' => 'Poll an SQS queue to process a message',
            [
                '--queue-duration=SECONDS', '(optional) Number of seconds the queue process will run for'
            ],
        ];
    }

    /**
     * On bootstrap
     *
     * @return void
     */
    public function onBootstrap()
    {
        // block session saving when running cli, as causes permissions errors
        if (PHP_SAPI === 'cli') {
            $handler = new Session\NullSaveHandler();
            $manager = new \Zend\Session\SessionManager();
            $manager->setSaveHandler($handler);
            \Zend\Session\Container::setDefaultManager($manager);
        }
    }

    /**
     * Get config
     *
     * @return array
     */
    public function getConfig()
    {
        return (include __DIR__ . '/../config/module.config.php');
    }

    /**
     * Get autoloader config
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }
}

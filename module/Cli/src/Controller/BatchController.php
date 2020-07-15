<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Api\Domain\Query;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Cli\Domain\Command as CliCommand;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Enqueue;
use Dvsa\Olcs\Cli\Domain\Command\PopulateLastLoginFromOpenAm;
use Dvsa\Olcs\Cli\Domain\Query as CliQuery;
use Dvsa\Olcs\Queue\Service\Message\CompaniesHouse\CompanyProfile;
use Dvsa\Olcs\Transfer\Command as TransferCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Zend\Http\Response;
use Zend\View\Model\ConsoleModel;

/**
 * BatchController
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class BatchController extends AbstractCliController
{
    /**
     * Perform database management tasks, eg changing is_irfo flags
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    public function databaseMaintenanceAction()
    {
        return $this->handleExitStatus(
            $this->handleCommand(
                [
                    Command\Organisation\FixIsIrfo::create([]),
                    Command\Organisation\FixIsUnlicenced::create([]),
                ]
            )
        );
    }

    /**
     * Find continuations that have not been process and generate reminders
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    public function digitalContinuationRemindersAction()
    {
        return $this->handleExitStatus(
            $this->handleCommand([Command\ContinuationDetail\DigitalSendReminders::create([])])
        );
    }

    /**
     * Run data retention rules
     *
     * @return \Zend\View\Model\ConsoleModel
     * @throws \Exception
     */
    public function dataRetentionRuleAction()
    {
        if ($this->params('populate')) {
            return $this->handleExitStatus($this->handleCommand([Command\DataRetention\Populate::create([])]));
        }

        $limit = $this->params('limit');
        if ($this->params('delete')) {
            $dto = Command\DataRetention\DeleteEntities::create(['limit' => $limit]);
            return $this->handleExitStatus($this->handleCommand([$dto]));
        }

        if ($this->params('precheck')) {
            if (!is_numeric($limit) || $limit < 1 || $limit != round($limit)) {
                $consoleModel = new ConsoleModel();
                $consoleModel->setResult("\nYou must specify a positive integer limit with --limit=x\n\n");
                $consoleModel->setErrorLevel(1);
                return($consoleModel);
            }
            return $this->handleExitStatus($this->handleCommand([Command\DataRetention\Precheck::create(['limit' => $limit])]));
        }

        if ($this->params('postcheck')) {
            $result = $this->handleQuery(Query\DataRetention\Postcheck::create([]));

            $consoleModel = new ConsoleModel();
            $consoleModel->setResult(json_encode($result, JSON_PRETTY_PRINT));

            $consoleModel->setErrorLevel(0);
            return $consoleModel;
        }
    }

    /**
     * Clean abandoned variations
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    public function cleanUpVariationsAction()
    {
        return $this->handleExitStatus($this->handleCommand([CliCommand\CleanUpAbandonedVariations::create([])]));
    }

    /**
     * Expire bus registrations that have passed the end date
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    public function expireBusRegistrationAction()
    {
        return $this->handleExitStatus($this->handleCommand([CliCommand\Bus\Expire::create([])]));
    }

    /**
     * Flag tasks as urgent
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    public function flagUrgentTasksAction()
    {
        return $this->handleExitStatus($this->handleCommand([TransferCommand\Task\FlagUrgentTasks::create([])]));
    }

    /**
     * Remove read audit action
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    public function removeReadAuditAction()
    {
        return $this->handleExitStatus($this->handleCommand([CliCommand\RemoveReadAudit::create([])]));
    }

    /**
     * Inspection request email action
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    public function inspectionRequestEmailAction()
    {
        return $this->handleExitStatus(
            $this->handleCommand(
                [
                    \Dvsa\Olcs\Email\Domain\Command\ProcessInspectionRequestEmail::create([]),
                ]
            )
        );
    }

    /**
     * Duplicate vehicle warning action
     *
     * @return ConsoleModel
     */
    public function duplicateVehicleWarningAction()
    {
        return $this->handleExitStatus(
            $this->handleCommand(
                [
                    Command\Vehicle\ProcessDuplicateVehicleWarnings::create([]),
                ]
            )
        );
    }

    /**
     * Duplicate vehicle removal action
     *
     * @return ConsoleModel
     */
    public function duplicateVehicleRemovalAction()
    {
        return $this->handleExitStatus(
            $this->handleCommand(
                [
                    Command\Vehicle\ProcessDuplicateVehicleRemoval::create([]),
                ]
            )
        );
    }

    /**
     * Process LicenceStatusRules
     *
     * @return ConsoleModel
     */
    public function licenceStatusRulesAction()
    {
        return $this->handleExitStatus(
            $this->handleCommand(
                [
                    Command\LicenceStatusRule\ProcessToRevokeCurtailSuspend::create([]),
                    Command\LicenceStatusRule\ProcessToValid::create([])
                ]
            )
        );
    }

    /**
     * Enqueue companies house compare action
     *
     * @return ConsoleModel
     */
    public function enqueueCompaniesHouseCompareAction()
    {
        $organisations = $this->handleQuery(CliQuery\CompaniesHouse\Organisations::create([]));

        return $this->handleExitStatus(
            $this->handleCommand(
                [
                    Enqueue::create([
                        'messageData' => $organisations,
                        'queueType' => CompanyProfile::class,
                        'messageType' => CompanyProfile::class
                    ])
                ]
            )
        );
    }

    /**
     * Find differences between Companies house and Olcs data
     *
     * @return ConsoleModel
     */
    public function companiesHouseVsOlcsDiffsExportAction()
    {
        $params = [
            'path' =>  $this->params('path'),
        ];

        return $this->handleExitStatus(
            $this->handleCommand([CliCommand\CompaniesHouseVsOlcsDiffsExport::create($params)])
        );
    }

    /**
     * Continuation not sought action
     *
     * @return ConsoleModel
     */
    public function continuationNotSoughtAction()
    {
        $dryRun = $this->isDryRun();
        $date = new DateTime(); // this could come from a CLI param if needed

        // we use a separate query and command so we can do more granular output..

        // get list of licences
        $dto = Query\Licence\ContinuationNotSoughtList::create(['date' => $date]);
        $result = $this->handleQuery($dto);
        $this->writeVerboseMessages("{$result['count']} Licence(s) found to change to CNS");
        $licences = $result['result'];

        if (count($licences) === 0) {
            return $this->handleExitStatus(0);
        }

        if (!$dryRun) {
            return $this->handleExitStatus(
                $this->handleCommand(
                    [
                        Command\Licence\EnqueueContinuationNotSought::create(
                            [
                                'licences' => $licences,
                                'date' => $date
                            ]
                        ),
                    ]
                )
            );
        }
        return $this->handleExitStatus(0);
    }

    /**
     * Create PSV licence surrender tasks
     *
     * @return ConsoleModel
     */
    public function createPsvLicenceSurrenderTasksAction()
    {
        $dryRun = $this->isDryRun();
        $date = new DateTime();

        $dto = Query\Licence\PsvLicenceSurrenderList::create(['date' => $date]);
        $result = $this->handleQuery($dto);
        $this->writeVerboseMessages("{$result['count']} PSV Licence(s) found to create surrender tasks");
        $licenceIds = $result['result'];

        if (count($licenceIds) !== 0 && !$dryRun) {
            return $this->handleExitStatus(
                $this->handleCommand(
                    [
                        Command\Licence\CreateSurrenderPsvLicenceTasks::create(
                            [
                                'ids' => $licenceIds,
                            ]
                        ),
                    ]
                )
            );
        }

        return $this->handleExitStatus(0);
    }

    /**
     * Process inbox documents action
     *
     * @return ConsoleModel
     */
    public function processInboxDocumentsAction()
    {
        return $this->handleExitStatus(
            $this->handleCommand(
                [
                    Command\Correspondence\ProcessInboxDocuments::create([]),
                ]
            )
        );
    }

    /**
     * Process NTU action
     *
     * @return ConsoleModel
     */
    public function processNtuAction()
    {
        $dryRun = $this->isDryRun();
        $date = new DateTime();
        $dto = Query\Application\NotTakenUpList::create(['date' => $date]);
        $result = $this->handleQuery($dto);
        if (is_array($result) && isset($result['result'])) {
            $this->writeVerboseMessages("{$result['count']} Application(s) found to change to NTU");
            $applications = $result['result'];
            $commands = [];
            foreach ($applications as $application) {
                $this->writeVerboseMessages("Processing Application ID {$application['id']}");
                $commands[] = TransferCommand\Application\NotTakenUpApplication::create(
                    [
                        'id' => $application['id']
                    ]
                );
            }
            if (!$dryRun) {
                return $this->handleExitStatus($this->handleCommand($commands));
            }
        }

        return $this->handleExitStatus(0);
    }

    /**
     * Process NTU action
     *
     * @return ConsoleModel
     */
    public function processCommunityLicencesAction()
    {
        $dryRun = $this->isDryRun();
        $date = (new DateTime())->setTime(0, 0, 0);

        $commands = [];

        $dto = CliQuery\CommunityLic\CommunityLicencesForSuspensionList::create(['date' => $date]);
        $result = $this->handleQuery($dto);
        $this->writeVerboseMessages("{$result['count']} community licence(s) found for suspension");
        if (is_array($result) && $result['count']) {
            $commands[] = CliCommand\CommunityLic\Suspend::create(
                ['communityLicenceIds' => array_column($result['result'], 'id')]
            );
        }

        $dto = CliQuery\CommunityLic\CommunityLicencesForActivationList::create(['date' => $date]);
        $result = $this->handleQuery($dto);
        $this->writeVerboseMessages("{$result['count']} community licence(s) found for activation");
        if (is_array($result) && $result['count']) {
            $commands[] = CliCommand\CommunityLic\Activate::create(
                ['communityLicenceIds' => array_column($result['result'], 'id')]
            );
        }

        if (!$dryRun && count($commands)) {
            return $this->handleExitStatus($this->handleCommand($commands));
        }

        return $this->handleExitStatus(0);
    }

    /**
     * Set a SystemParameter
     *
     * @return ConsoleModel
     */
    public function setSystemParameterAction()
    {
        $name = $this->params('name');
        $value = $this->params('value');

        $dto = Command\SystemParameter\Update::create(
            [
                'id' => $name,
                'paramValue' => $value,
            ]
        );

        $result = $this->handleCommand([$dto]);

        if ($result === Response::STATUS_CODE_404) {
            $this->writeVerboseMessages("SystemParameter with name '{$name}' was not found.");
        }

        return $this->handleExitStatus($result);
    }

    /**
     * Resolve pending CPMS payments
     *
     * @return ConsoleModel
     */
    public function resolvePaymentsAction()
    {
        $dto = Command\Transaction\ResolveOutstandingPayments::create([]);

        $result = $this->handleCommand([$dto]);

        return $this->handleExitStatus($result);
    }

    /**
     * Create mobile compliance VI extract files
     *
     * @return ConsoleModel
     */
    public function createViExtractFilesAction()
    {
        $params = [];
        if ($this->params('vhl')) {
            $params['vhl'] = true;
        }
        if ($this->params('tnm')) {
            $params['tnm'] = true;
        }
        if ($this->params('op')) {
            $params['op'] = true;
        }
        if ($this->params('oc')) {
            $params['oc'] = true;
        }
        if ($this->params('all')) {
            $params['all'] = true;
        }
        $params['path'] = $this->params('path');

        return $this->handleExitStatus(
            $this->handleCommand(
                [
                    CliCommand\SetViFlags::create([]),
                    CliCommand\CreateViExtractFiles::create($params),
                ]
            )
        );
    }

    /**
     * Create csv files for data.org.uk
     *
     * @return ConsoleModel
     */
    public function dataGovUkExportAction()
    {
        $params = [
            'reportName' =>  $this->params('report-name'),
            'path' =>  $this->params('path'),
        ];

        return $this->handleExitStatus(
            $this->handleCommand([CliCommand\DataGovUkExport::create($params)])
        );
    }

    /**
     * Create csv files for Northern Ireland
     *
     * @return ConsoleModel
     */
    public function dataDvaNiExportAction()
    {
        $params = [
            'reportName' =>  $this->params('report-name'),
            'path' =>  $this->params('path'),
        ];

        return $this->handleExitStatus(
            $this->handleCommand([CliCommand\DataDvaNiExport::create($params)])
        );
    }

    /**
     * Create csv files for data.org.uk
     *
     * @return ConsoleModel
     */
    public function importUsersFromCsvAction()
    {
        $params = [
            'csvPath' =>  $this->params('csv-path'),
            'resultCsvPath' =>  $this->params('result-csv-path'),
        ];

        return $this->handleExitStatus(
            $this->handleCommand([CliCommand\ImportUsersFromCsv::create($params)])
        );
    }

    /**
     * Send Last TM letters
     *
     * @return ConsoleModel
     */
    public function lastTmLetterAction()
    {
        return $this->handleExitStatus(
            $this->handleCommand([CliCommand\LastTmLetter::create([])])
        );
    }

    /**
     * Permits
     *
     * @return ConsoleModel
     */
    public function permitsAction()
    {
        if ($this->params('close-expired-windows')) {
            $params = [
                'since' =>  $this->params('since') ? : '-1 day',
            ];

            return $this->handleExitStatus(
                $this->handleCommand([CliCommand\Permits\CloseExpiredWindows::create($params)])
            );
        } elseif ($this->params('mark-expired-permits')) {
            return $this->handleExitStatus(
                $this->handleCommand([CliCommand\Permits\MarkExpiredPermits::create([])])
            );
        } elseif ($this->params('withdraw-unpaid')) {
            return $this->handleExitStatus(
                $this->handleCommand([CliCommand\Permits\WithdrawUnpaidIrhp::create([])])
            );
        }
    }

    /**
     * Populate lastLoginAt column in user with data from OpenAM
     *
     * @throws \Exception
     */
    public function populateLastLoginAction()
    {
        $params['isLiveRun'] = $this->params('live') ? true : false;
        $params['limit'] = $this->params('limit');
        $params['batchSize'] = $this->params('batch-size');
        if ($this->params('show-progress')) {
            $progressBar = new ProgressBar(new ConsoleOutput());

            if ($this->params('v') || $this->params('verbose')) {
                $progressBar->setFormat('very_verbose');
            }

            $params['progressBar'] = $progressBar;
        }

        $params['console'] = $this->getConsole();

        $this->handleExitStatus(
            $this->handleCommand([PopulateLastLoginFromOpenAm::create($params)])
        );
    }
}

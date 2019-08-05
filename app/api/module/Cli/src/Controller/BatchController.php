<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Query;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Cli\Domain\Command as CliCommand;
use Dvsa\Olcs\Cli\Domain\Query as CliQuery;
use Dvsa\Olcs\Transfer\Command as TransferCommand;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Olcs\Logging\Log\Logger;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\View\Model\ConsoleModel;

/**
 * BatchController
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class BatchController extends AbstractConsoleController
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

        if ($this->params('delete')) {
            $limit = $this->params('limit');
            $dto = Command\DataRetention\DeleteEntities::create(['limit' => $limit]);
            return $this->handleExitStatus($this->handleCommand([$dto]));
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
     * Withdraw ecmt applications where the fee hasn't been paid on time
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    public function withdrawUnpaidEcmtApplicationsAction()
    {
        return $this->handleExitStatus(
            $this->handleCommand(
                [
                    CliCommand\Permits\WithdrawUnpaidEcmt::create([]),
                    CliCommand\Permits\WithdrawUnpaidIrhp::create([]),
                ]
            )
        );
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
        return $this->handleExitStatus(
            $this->handleCommand(
                [
                    Command\CompaniesHouse\EnqueueOrganisations::create([]),
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
     * Is verbose
     *
     * @return boolean
     */
    private function isVerbose()
    {
        return $this->params('verbose') || $this->params('v');
    }

    /**
     * Is dry run
     *
     * @return boolean
     */
    private function isDryRun()
    {
        return $this->params('dryrun') || $this->params('d');
    }

    /**
     * Using this method ensures the calling CLI environment gets an appropriate
     * exit code from the process.
     *
     * @param int $result exit code, should be non-zero if there was an error
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    private function handleExitStatus($result)
    {
        $model = new ConsoleModel();
        $model->setErrorLevel($result);
        return $model;
    }

    /**
     * Handle DTO commands
     *
     * @param array $dto dto
     *
     * @return int Response code
     */
    protected function handleCommand(array $dto)
    {
        try {
            $count = 0;
            foreach ($dto as $dtoCommand) {
                $count++;
                $this->writeVerboseMessages("Handle command ". $count .' '. get_class($dtoCommand));

                /** @var \Dvsa\Olcs\Api\Domain\Command\Result $result */
                $result = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dtoCommand);

                $this->writeVerboseMessages($result->getMessages());
            }
        } catch (Exception\NotFoundException $e) {
            $this->writeVerboseMessages(['NotFoundException', $e->getMessage()], \Zend\Log\Logger::WARN);
            return 404;
        } catch (Exception\Exception $e) {
            $this->writeVerboseMessages($e->getMessages(), \Zend\Log\Logger::ERR);
            return 400;
        } catch (\Exception $e) {
            $this->writeVerboseMessages($e->getMessage(), \Zend\Log\Logger::ERR);
            return 500;
        }

        return 0;
    }

    /**
     * Handle DTO query
     *
     * @param QueryInterface $dto dto
     *
     * @return mixed $result|false
     */
    protected function handleQuery(QueryInterface $dto)
    {
        try {
            $this->writeVerboseMessages("Handle query ". get_class($dto));
            return $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($dto);
        } catch (Exception\NotFoundException $e) {
            $this->writeVerboseMessages(['NotFoundException', $e->getMessage()], \Zend\Log\Logger::WARN);
        } catch (Exception\Exception $e) {
            $this->writeVerboseMessages($e->getMessages(), \Zend\Log\Logger::ERR);
        } catch (\Exception $e) {
            $this->writeVerboseMessages([$e->getMessage()], \Zend\Log\Logger::ERR);
        }

        return false;
    }

    /**
     * Write verbose messages, ie only if verbose flag is set
     *
     * @param array|string $messages    Message to write
     * @param int          $logPriority One of \Zend\Log\Logger::*
     *
     * @return void
     */
    protected function writeVerboseMessages($messages, $logPriority = \Zend\Log\Logger::DEBUG)
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        if ($this->isVerbose()) {
            $this->writeMessages($messages);
        }
        Logger::log(
            $logPriority,
            json_encode($messages)
        );
    }

    /**
     * Write messages to the console
     *
     * @param array $messages Message to write
     *
     * @return void
     */
    protected function writeMessages($messages)
    {
        foreach ($messages as $message) {
            $this->getConsole()->writeLine((new \DateTime())->format(\DateTime::W3C) .' '. $message);
        }
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
        }
    }
}

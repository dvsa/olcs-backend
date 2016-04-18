<?php

/**
 * BatchController
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Cli\Domain\Command\RemoveReadAudit;
use Dvsa\Olcs\Cli\Domain\Command\CreateViExtractFiles;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\View\Model\ConsoleModel;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Api\Domain\Query;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command as TransferCommand;

/**
 * BatchController
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class BatchController extends AbstractConsoleController
{
    /**
     * @return \Zend\View\Model\ConsoleModel
     */
    public function removeReadAuditAction()
    {
        return $this->handleExitStatus($this->handleCommand([RemoveReadAudit::create([])]));
    }

    /**
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

        // build array of commands (once per licence)
        $commands = [];
        foreach ($licences as $licence) {
            $commands[] = Command\Licence\ProcessContinuationNotSought::create(
                [
                    'id' => $licence['id'],
                    'version' => $licence['version'],
                ]
            );
        }

        // add a command to send email summary
        $commands[] = Command\Email\SendContinuationNotSought::create(
            [
                'date' => $date,
                'licences' => $licences,
            ]
        );

        // execute commands
        if (!$dryRun) {

            // @todo This process is memory intensive and slow, recommend to BJSS that cli jobs need min 512M
            // Then remove this
            $memoryLimit = ini_set('memory_limit', '512M');

            $this->writeVerboseMessages("Processing commands");
            $result = $this->handleCommand($commands);

            // restore memory limit to original value
            ini_set('memory_limit', $memoryLimit);

            return $this->handleExitStatus($result);
        }

        return $this->handleExitStatus(0);
    }

    /**
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

    public function processNtuAction()
    {
        $dryRun = $this->isDryRun();
        $date = new DateTime();
        $dto = Query\Application\NotTakenUpList::create(['date' => $date]);
        $result = $this->handleQuery($dto);
        $this->writeVerboseMessages("{$result['count']} Application(s) found to change to NTU");
        $applications = $result['result'];
        $commands = [];
        foreach ($applications as $application) {
            $this->writeVerboseMessages("Processing Application ID {$application->getId()}");
            $commands[] = TransferCommand\Application\NotTakenUpApplication::create(
                [
                    'id' => $application->getId()
                ]
            );
        }
        if (!$dryRun) {
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

        if ($result === 404) {
            $this->writeMessages("SystemParameter with name '{$name}' was not found.");
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
                    \Dvsa\Olcs\Cli\Domain\Command\SetViFlags::create([]),
                    CreateViExtractFiles::create($params),
                ]
            )
        );
    }

    /**
     * @return boolean
     */
    private function isVerbose()
    {
        return $this->params('verbose') || $this->params('v');
    }

    /**
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
     * @param array $dto
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
                $result = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dtoCommand);
                $this->writeVerboseMessages($result->getMessages());
            }
        } catch (Exception\NotFoundException $e) {
            $this->writeVerboseMessages(['NotFoundException', $e->getMessage()]);
            return 404;
        } catch (Exception\Exception $e) {
            $this->writeVerboseMessages($e->getMessages());
            return 400;
        } catch (\Exception $e) {
            $this->writeVerboseMessages($e->getMessage());
            return 500;
        }

        return 0;
    }

    /**
     * Handle DTO query
     *
     * @param QueryInterface $dto
     *
     * @return mixed $result|false
     */
    protected function handleQuery(QueryInterface $dto)
    {
        try {
            $this->writeVerboseMessages("Handle command ". get_class($dto));
            return $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($dto);
        } catch (Exception\NotFoundException $e) {
            $this->writeVerboseMessages(['NotFoundException', $e->getMessage()]);
        } catch (Exception\Exception $e) {
            $this->writeVerboseMessages($e->getMessages());
        } catch (\Exception $e) {
            $this->writeVerboseMessages($e->getMessage());
        }

        return false;
    }

    /**
     * Write verbose messages, ie only if verbose flag is set
     *
     * @param array|string $messages
     */
    protected function writeVerboseMessages($messages)
    {
        if ($this->isVerbose()) {
            $this->writeMessages($messages);
        }
    }

    /**
     * Write messages to the console
     *
     * @param array|string $messages
     *
     * @return void
     */
    protected function writeMessages($messages)
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        foreach ($messages as $message) {
            $this->getConsole()->writeLine((new \DateTime())->format(\DateTime::W3C) .' '. $message);
        }
    }
}

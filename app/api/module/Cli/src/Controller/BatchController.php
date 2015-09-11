<?php

/**
 * BatchController
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Cli\Controller;

use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\View\Model\ConsoleModel;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Api\Domain\Query;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * BatchController
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class BatchController extends AbstractConsoleController
{
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
        $dryRun = $this->getRequest()->getParam('dryrun') || $this->getRequest()->getParam('d');
        $date = new DateTime('2016-02-01'); // this could come from a param if needed

        // we use a separate query and command so we can do more granular output..

        // get list of licences
        $dto = Query\Licence\ContinuationNotSoughtList::create(['date' => $date]);
        $result = $this->handleQuery($dto);
        $this->writeVerboseMessages("{$result['count']} Licence(s) found to change to CNS");

        // build array of commands (once per licence)
        $commands = [];
        foreach ($result['result'] as $licenceData) {
            $this->writeVerboseMessages("Processing Licence ID {$licenceData['id']}");
            $commands[] = Command\Licence\ProcessContinuationNotSought::create(
                [
                    'id' => $licenceData['id'],
                    'version' => $licenceData['version'],
                ]
            );
        }

        // $commands[] =             // Email\continuationNotSoughtAction

        // execute commands
        if (!$dryRun) {
            return $this->handleExitStatus($this->handleCommand($commands));
        }

        return $this->handleExitStatus(0);
    }

    /**
     * @return boolean
     */
    private function isVerbose()
    {
        return $this->params('verbose') || $this->params('v');
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
        $this->writeVerboseMessages((new \DateTime())->format(\DateTime::W3C));

        try {
            $result = new Command\Result();
            foreach ($dto as $dtoCommand) {
                $result->merge($this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dtoCommand));
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

        $this->writeVerboseMessages($result->getMessages());

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
        $this->writeVerboseMessages((new \DateTime())->format(\DateTime::W3C));

        try {
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
            $this->getConsole()->writeLine($message);
        }
    }
}

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

/**
 * BatchController
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class BatchController extends AbstractConsoleController
{
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
                    \Dvsa\Olcs\Api\Domain\Command\LicenceStatusRule\ProcessToRevokeCurtailSuspend::create([]),
                    \Dvsa\Olcs\Api\Domain\Command\LicenceStatusRule\ProcessToValid::create([])
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
                    \Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\EnqueueOrganisations::create([]),
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
     * Using this method ensures the calling CLI environment gets an appropriate
     * exit code from the process.
     *
     * @param int $result exit code, should be non-zero if there was an error
     * @return Zend\View\Model\ConsoleModel
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
            $result = new \Dvsa\Olcs\Api\Domain\Command\Result();
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

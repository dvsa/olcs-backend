<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Olcs\Logging\Log\Logger;
use Laminas\Mvc\Console\Controller\AbstractConsoleController;
use Laminas\Mvc\Console\View\ViewModel as ConsoleModel;

class AbstractCliController extends AbstractConsoleController
{
    protected CommandHandlerManager $commandHandlerManager;
    protected QueryHandlerManager $queryHandlerManager;

    public function __construct(
        QueryHandlerManager $queryHandlerManager,
        CommandHandlerManager $commandHandlerManager
    ) {
        $this->queryHandlerManager = $queryHandlerManager;
        $this->commandHandlerManager = $commandHandlerManager;
    }

    /**
     * Is verbose
     *
     * @return boolean
     */
    protected function isVerbose()
    {
        return $this->params('verbose') || $this->params('v');
    }

    /**
     * Write verbose messages, ie only if verbose flag is set
     *
     * @param array|string $messages Message to write
     * @param int $logPriority One of \Laminas\Log\Logger::*
     *
     * @return void
     * @throws \Exception
     */
    protected function writeVerboseMessages($messages, $logPriority = \Laminas\Log\Logger::DEBUG)
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
     * Using this method ensures the calling CLI environment gets an appropriate
     * exit code from the process.
     *
     * @param int $result exit code, should be non-zero if there was an error
     *
     * @return \Laminas\Mvc\Console\View\ViewModel
     */
    protected function handleExitStatus($result)
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
     * @throws \Exception
     */
    protected function handleCommand(array $dto)
    {
        try {
            $count = 0;
            foreach ($dto as $dtoCommand) {
                $count++;
                $this->writeVerboseMessages("Handle command " . $count . ' ' . get_class($dtoCommand));

                /** @var \Dvsa\Olcs\Api\Domain\Command\Result $result */
                $result = $this->commandHandlerManager->handleCommand($dtoCommand);

                $this->writeVerboseMessages($result->getMessages());
            }
        } catch (NotFoundException $e) {
            $this->writeVerboseMessages(['NotFoundException', $e->getMessage()], \Laminas\Log\Logger::WARN);
            return 404;
        } catch (Exception\Exception $e) {
            $this->writeVerboseMessages($e->getMessages(), \Laminas\Log\Logger::ERR);
            return 400;
        } catch (\Exception $e) {
            $this->writeVerboseMessages($e->getMessage(), \Laminas\Log\Logger::ERR);
            return 500;
        }

        return 0;
    }

    /**
     * Write messages to the console
     *
     * @param array $messages Message to write
     *
     * @return void
     * @throws \Exception
     */
    protected function writeMessages($messages)
    {
        foreach ($messages as $message) {
            $this->getConsole()->writeLine((new \DateTime())->format(\DateTime::W3C) . ' ' . $message);
        }
    }

    /**
     * Is dry run
     *
     * @return boolean
     */
    protected function isDryRun()
    {
        return $this->params('dryrun') || $this->params('d');
    }

    /**
     * Handle DTO query
     *
     * @param QueryInterface $dto dto
     *
     * @return mixed $result|false
     * @throws \Exception
     */
    protected function handleQuery(QueryInterface $dto)
    {
        try {
            $this->writeVerboseMessages("Handle query " . get_class($dto));
            return $this->queryHandlerManager->handleQuery($dto);
        } catch (NotFoundException $e) {
            $this->writeVerboseMessages(['NotFoundException', $e->getMessage()], \Laminas\Log\Logger::WARN);
        } catch (Exception\Exception $e) {
            $this->writeVerboseMessages($e->getMessages(), \Laminas\Log\Logger::ERR);
        } catch (\Exception $e) {
            $this->writeVerboseMessages([$e->getMessage()], \Laminas\Log\Logger::ERR);
        }

        return false;
    }
}

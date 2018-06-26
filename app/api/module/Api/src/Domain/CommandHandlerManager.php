<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\DisabledHandlerException;
use Dvsa\Olcs\Transfer\Command\LoggerOmitContentInterface;
use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Zend\ServiceManager\Exception\RuntimeException;

/**
 * Command Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandHandlerManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $config = null)
    {
        $this->setShareByDefault(false);

        if ($config) {
            $config->configureServiceManager($this);
        }
    }

    public function handleCommand(CommandInterface $command, $validate = true)
    {
        $start = microtime(true);

        $commandFqcn = get_class($command);

        $commandHandler = $this->get($commandFqcn);

        if ($commandHandler instanceof TransactioningCommandHandler) {
            $validateCommandHandler = $commandHandler->getWrapped();
        } else {
            $validateCommandHandler = $commandHandler;
        }

        if ($command instanceof LoggerOmitContentInterface) {
            $data = ['*** OMITTED ***'];
        } else {
            $data = $command->getArrayCopy();
        }

        Logger::debug(
            'Command Received: ' . $commandFqcn,
            ['data' => ['commandData' => $data]]
        );

        $commandHandlerFqcn = get_class($validateCommandHandler);

        if (!$validateCommandHandler->isEnabled()) {
            $exception = new DisabledHandlerException($commandHandlerFqcn);
            Logger::warn(get_class($this) . ': ' . $exception->getMessage());
            throw $exception;
        }

        if ($validate) {
            $this->validateDto($command, $commandHandlerFqcn);
        }

        $response = $commandHandler->handleCommand($command);

        Logger::debug(
            'Command Handler Response: ' . $commandHandlerFqcn,
            [
                'data' => [
                    'response' => (array)$response,
                    'time' => round(microtime(true) - $start, 5),
                ]
            ]
        );

        return $response;
    }

    public function validatePlugin($plugin)
    {
        if (!($plugin instanceof CommandHandlerInterface)) {
            throw new RuntimeException('Command handler does not implement CommandHandlerInterface');
        }
    }

    /**
     * Validate command data
     * 
     * @param CommandInterface $dto
     * @param string           $queryHandlerFqcl
     * 
     * @throws ForbiddenException
     */
    protected function validateDto($dto, $queryHandlerFqcl)
    {
        /** @var ValidationHandlerManager $vhm */
        $vhm = $this->getServiceLocator()->get('ValidationHandlerManager');

        $validationHandler = $vhm->get($queryHandlerFqcl);

        if (!$validationHandler->isValid($dto)) {
            Logger::debug(
                'DTO Failed validation',
                [
                    'handler' => $queryHandlerFqcl,
                    'data' => $dto->getArrayCopy(),
                ]
            );
            throw new ForbiddenException('You do not have access to this resource');
        }
    }
    /**
     * We want to log some exceptions (right now we only log an attempt to call a disabled handler)
     *
     * @param \Exception $e exception
     *
     * @return void
     * @throws \Exception rethrows original Exception
     */
    private function logException(DisabledHandlerException $e)
    {
        Logger::warn(get_class($this) . ': ' . $e->getMessage());
    }
}

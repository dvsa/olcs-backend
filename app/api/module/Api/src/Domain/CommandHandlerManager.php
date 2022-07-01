<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\DisabledHandlerException;
use Dvsa\Olcs\Transfer\Command\LoggerOmitContentInterface;
use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
use Olcs\Logging\Log\Logger;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;

/**
 * Command Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandHandlerManager extends AbstractPluginManager
{
    use PluginManagerTrait;

    protected $instanceOf = CommandHandlerInterface::class;

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

        $validateCommandHandler->checkEnabled();

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

    /**
     * Validate command data
     *
     * @param CommandInterface $dto
     * @param string           $queryHandlerFqcl
     *
     * @return void
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
}

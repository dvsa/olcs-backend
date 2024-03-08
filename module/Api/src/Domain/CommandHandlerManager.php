<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface as ValidationHandlerInterface;
use Dvsa\Olcs\Transfer\Command\LoggerOmitContentInterface;
use Psr\Container\ContainerInterface;
use Olcs\Logging\Log\Logger;
use Laminas\ServiceManager\AbstractPluginManager;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;

/**
 * @template-extends AbstractPluginManager<CommandHandlerInterface>
 */
class CommandHandlerManager extends AbstractPluginManager
{
    protected $instanceOf = CommandHandlerInterface::class;
    private ValidationHandlerManager $validationHandlerManager;

    public function __construct(ContainerInterface $container, array $config = [])
    {
        $this->validationHandlerManager = $container->get('ValidationHandlerManager');
        parent::__construct($container, $config);
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
        /** @var ValidationHandlerInterface $validationHandler */
        $validationHandler = $this->validationHandlerManager->get($queryHandlerFqcl);

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

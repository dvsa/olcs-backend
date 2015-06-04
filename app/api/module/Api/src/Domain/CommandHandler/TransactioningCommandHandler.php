<?php

/**
 * Class TransactioningCommandHandler
 * @package Dvsa\Olcs\Api\Domain\CommandHandler
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class TransactioningCommandHandler
 * @package Dvsa\Olcs\Api\Domain\CommandHandler
 */
final class TransactioningCommandHandler implements CommandHandlerInterface
{
    /**
     * @var TransactionManagerInterface
     */
    private $repo;

    /**
     * @var CommandHandlerInterface
     */
    private $wrapped;

    /**
     * @param CommandHandlerInterface $wrapped
     * @param TransactionManagerInterface $repo
     */
    public function __construct(CommandHandlerInterface $wrapped, TransactionManagerInterface $repo)
    {
        $this->repo = $repo;
        $this->wrapped = $wrapped;
    }

    /**
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        try {
            $this->repo->beginTransaction();
            $result = $this->wrapped->handleCommand($command);
            $this->repo->commit();
        } catch (\Exception $e) {
            $this->repo->rollback();
            throw $e;
        }

        return $result;
    }
}
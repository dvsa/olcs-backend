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
class TransactioningCommandHandler implements CommandHandlerInterface
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

    public function getWrapped()
    {
        return $this->wrapped;
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
            return $result;

        } catch (\Exception $e) {
            if (method_exists($this->wrapped, 'rollbackCommand')) {
                // wrapped command rollback
                $this->wrapped->rollbackCommand($command, $e);
            }
            $this->repo->rollback();
            throw $e;
        }
    }
}

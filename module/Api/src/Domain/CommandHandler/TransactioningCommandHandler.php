<?php

/**
 * Abstract Command Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;

/**
 * Abstract Command Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class TransactioningCommandHandler implements CommandHandlerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repo;

    /**
     * @var CommandHandlerInterface
     */
    private $wrapped;

    public function __construct(CommandHandlerInterface $wrapped, RepositoryInterface $repo)
    {
        $this->repo = $repo;
        $this->wrapped = $wrapped;
    }
    /**
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
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
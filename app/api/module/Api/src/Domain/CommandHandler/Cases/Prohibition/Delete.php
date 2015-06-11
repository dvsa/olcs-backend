<?php

/**
 * Delete Conviction
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Conviction;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Prohibition as Entity;
use Dvsa\Olcs\Api\Domain\Repository\Prohibition as Repository;
use Dvsa\Olcs\Api\Entity as Entities;
use Dvsa\Olcs\Transfer\Command\Cases\Prohibition\Delete as DeleteCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Doctrine\ORM\Query;

/**
 * Delete Conviction
 */
final class Delete extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Prohibition';

    /**
     * Delete
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $command DeleteCommand For traceability */

        $result = new Result();

        /** @var Repository $repo */
        $repo = $this->getRepo();

        /* @var Entity $entity */
        $entity = $repo->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        $repo->delete($entity);

        $result->addMessage('Deleted');

        return $result;
    }
}

<?php

/**
 * Delete Prohibition
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Prohibition\Defect;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Prohibition\ProhibitionDefect as Entity;
use Dvsa\Olcs\Api\Domain\Repository\ProhibitionDefect as Repository;
use Dvsa\Olcs\Api\Entity as Entities;
use Dvsa\Olcs\Transfer\Command\Cases\Prohibition\Defect\Delete as DeleteCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Doctrine\ORM\Query;

/**
 * Delete Prohibition
 */
final class Delete extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ProhibitionDefect';

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

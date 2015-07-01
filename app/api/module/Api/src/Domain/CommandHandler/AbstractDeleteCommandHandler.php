<?php

/**
 * Delete Abstract
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Prohibition\Prohibition as Entity;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface as Repository;
use Dvsa\Olcs\Api\Entity as Entities;
use Dvsa\Olcs\Transfer\Command\AbstractDeleteCommand as DeleteCommand;
use Doctrine\ORM\Query;

/**
 * Delete Abstract
 */
abstract class AbstractDeleteCommandHandler extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName;

    /**
     * Delete Command Handler Abstract
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

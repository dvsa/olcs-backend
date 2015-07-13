<?php

/**
 * Delete Bus
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface as Repository;
use Dvsa\Olcs\Api\Entity as Entities;
use Dvsa\Olcs\Transfer\Command\AbstractDeleteCommand as DeleteCommand;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\Bus\PreviousVariationByRouteNo;

/**
 * Delete Bus
 */
final class DeleteBus extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Bus';

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

        /* @var BusReg $busReg */
        $busReg = $repo->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $busReg->canDelete();

        $routeNoQuery = [
            'sort' => 'variationNo',
            'order' => 'DESC',
            'page' => 1,
            'limit' => 1,
            'routeNo' => $busReg->getRouteNo(),
            'variationNo' => $busReg->getVariationNo(),
        ];

        $previousBusReg = $repo->fetchList(PreviousVariationByRouteNo::create($routeNoQuery), Query::HYDRATE_OBJECT);

        $repo->delete($busReg);

        $result->addMessage('Deleted');

        if ($previousBusReg->count()) {
            $result->addId('previousBusRegId', $previousBusReg->current()->getId());
        }

        return $result;
    }
}

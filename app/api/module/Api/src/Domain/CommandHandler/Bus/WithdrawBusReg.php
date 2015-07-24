<?php

/**
 * Withdraw BusReg
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Withdraw BusReg
 */
final class WithdrawBusReg extends AbstractCommandHandler
{
    protected $repoServiceName = 'Bus';

    public function handleCommand(CommandInterface $command)
    {
        $busReg = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $busReg->withdraw(
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_WITHDRAWN),
            $this->getRepo()->getRefdataReference($command->getReason())
        );

        $this->getRepo()->save($busReg);

        $result = new Result();
        $result->addId('bus', $busReg->getId());
        $result->addMessage('Bus Reg withdrawn successfully');

        return $result;
    }
}

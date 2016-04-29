<?php

/**
 * Withdraw BusReg
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrWithdrawn;

/**
 * Withdraw BusReg
 */
final class WithdrawBusReg extends AbstractCommandHandler
{
    use QueueAwareTrait;

    protected $repoServiceName = 'Bus';

    public function handleCommand(CommandInterface $command)
    {
        /** @var BusRegEntity $busReg */
        $busReg = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $busReg->withdraw(
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_WITHDRAWN),
            $this->getRepo()->getRefdataReference($command->getReason())
        );

        $this->getRepo()->save($busReg);

        $result = new Result();
        $result->addId('bus', $busReg->getId());
        $result->addMessage('Bus Reg withdrawn successfully');

        if ($busReg->isFromEbsr()) {
            $ebsrId = $busReg->getEbsrSubmissions()->first()->getId();
            $result->merge($this->handleSideEffect($this->createEbsrWithdrawnCmd($ebsrId)));
        }

        return $result;
    }

    /**
     * @param int $ebsrId
     * @return SendEbsrWithdrawn
     */
    private function createEbsrWithdrawnCmd($ebsrId)
    {
        return $this->emailQueue(SendEbsrWithdrawn::class, ['id' => $ebsrId], $ebsrId);
    }
}

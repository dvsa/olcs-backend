<?php

/**
 * Refuse BusReg By Short Notice
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRefused;

/**
 * Refuse BusReg By Short Notice
 */
final class RefuseBusRegByShortNotice extends AbstractCommandHandler
{
    protected $repoServiceName = 'Bus';

    public function handleCommand(CommandInterface $command)
    {
        /** @var BusRegEntity $busReg */
        $busReg = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $busReg->refuseByShortNotice(
            $command->getReason()
        );

        $this->getRepo()->save($busReg);

        $result = new Result();
        $result->addId('bus', $busReg->getId());
        $result->addMessage('Bus Reg refused successfully');

        if ($busReg->isFromEbsr()) {
            $ebsrId = $busReg->getEbsrSubmissions()->first()->getId();
            $result->merge($this->handleSideEffect($this->createEbsrRefusedCmd($ebsrId)));
        }

        return $result;
    }

    /**
     * @param int $ebsrId
     * @return SendEbsrRefused
     */
    private function createEbsrRefusedCmd($ebsrId)
    {
        return SendEbsrRefused::create(['id' => $ebsrId]);
    }
}

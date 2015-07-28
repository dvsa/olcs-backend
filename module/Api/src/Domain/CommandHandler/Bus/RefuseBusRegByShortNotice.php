<?php

/**
 * Refuse BusReg By Short Notice
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Refuse BusReg By Short Notice
 */
final class RefuseBusRegByShortNotice extends AbstractCommandHandler
{
    protected $repoServiceName = 'Bus';

    public function handleCommand(CommandInterface $command)
    {
        $busReg = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $busReg->refuseByShortNotice(
            $command->getReason()
        );

        $this->getRepo()->save($busReg);

        $result = new Result();
        $result->addId('bus', $busReg->getId());
        $result->addMessage('Bus Reg refused successfully');

        return $result;
    }
}

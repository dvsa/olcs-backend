<?php

/**
 * Create Cancellation
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Transfer\Command\Bus\CreateCancellation as Cmd;

/**
 * Create Cancellation
 */
final class CreateCancellation extends AbstractCommandHandler
{
    protected $repoServiceName = 'Bus';

    public function handleCommand(CommandInterface $command)
    {
        $bus = $this->createBusRegObject($command);

        $this->getRepo()->save($bus);

        $result = new Result();
        $result->addId('bus', $bus->getId());
        $result->addMessage('Cancellation created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Bus
     */
    private function createBusRegObject(Cmd $command)
    {
        $busReg = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        return $busReg->createVariation(
            $this->getRepo()->getRefdataReference(BusReg::STATUS_CANCEL),
            $this->getRepo()->getRefdataReference(BusReg::STATUS_CANCEL)
        );
    }
}

<?php

/**
 * Create Bus
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Bus\CreateBus as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\CreateBusFee as CreateBusFeeCmd;

/**
 * Create Bus
 */
final class CreateBus extends AbstractCommandHandler
{
    protected $repoServiceName = 'Bus';

    public function handleCommand(CommandInterface $command)
    {
        $bus = $this->createBusRegObject($command);

        $this->getRepo()->save($bus);

        $result = new Result();
        $result->addId('bus', $bus->getId());
        $result->addMessage('Bus created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return BusReg
     */
    private function createBusRegObject(Cmd $command)
    {
        return BusReg::createNew(
            $this->getRepo()->getReference(Licence::class, $command->getLicence()),
            $this->getRepo()->getRefdataReference(BusReg::STATUS_NEW),
            $this->getRepo()->getRefdataReference(BusReg::STATUS_NEW),
            $this->getRepo()->getRefdataReference(BusReg::SUBSIDY_NO),
            $this->getRepo()->getReference(BusNoticePeriod::class, BusNoticePeriod::NOTICE_PERIOD_OTHER)
        );
    }
}

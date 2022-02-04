<?php

declare (strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRegRepo;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Transfer\Command\Bus\UpdateEndDate as UpdateEndDateCmd;

final class UpdateEndDate extends AbstractCommandHandler
{
    protected $repoServiceName = 'Bus';

    const MSG_SUCCESS = 'Bus registration end date updated';

    /**
     * Update the bus registration end date, only allowed for registered records (see VOL-2677)
     *
     * @param CommandInterface $command Command
     *
     * @return Result
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function handleCommand(CommandInterface $command): Result
    {
        assert($command instanceof UpdateEndDateCmd);

        $busRegRepo = $this->getRepo();
        assert($busRegRepo instanceof BusRegRepo);

        $busRegId = $command->getId();
        $busReg = $busRegRepo->fetchById($busRegId);
        assert($busReg instanceof BusReg);

        $busReg->updateEndDate($command->getEndDate());
        $this->getRepo()->save($busReg);

        $this->result->addId('BusReg', $busRegId);
        $this->result->addMessage(self::MSG_SUCCESS);

        return $this->result;
    }
}

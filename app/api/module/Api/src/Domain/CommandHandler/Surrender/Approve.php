<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Licence\SurrenderLicence;
use Dvsa\Olcs\Transfer\Command\Surrender\Update as UpdateSurrender;

class Approve extends AbstractSurrenderCommandHandler
{
    /**
     * @param CommandInterface $command
     * @return Result
     * @throws RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = $this->handleSideEffect(UpdateSurrender::create(
            [
                'id' => $command->getId(),
                'status' => Surrender::SURRENDER_STATUS_APPROVED
            ]
        ));
        $this->result->addMessage($result);

        $result = $this->handleSideEffect(SurrenderLicence::create(
            [
                'id' => $command->getId(),
                'surrenderDate' => $command->getSurrenderDate(),
                'terminated' => false
            ]
        ));

        $this->result->merge($result);

        return $this->result;
    }
}

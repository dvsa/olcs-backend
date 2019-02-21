<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Surrender\Delete as DeleteSurrender;
use Dvsa\Olcs\Transfer\Query\Surrender\PreviousLicenceStatus;

class Withdraw extends AbstractSurrenderCommandHandler
{

    protected $extraRepos = ['Licence'];

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = $this->handleSideEffect(DeleteSurrender::create(
            [
                'id' => $command->getId()
            ]
        ));
        $this->result->addMessage($result);

        /** @var Licence $licence */
        $licence = $this->getRepo('Licence')->fetchById($command->getId());
        $previousStatus = $this->handleQuery(PreviousLicenceStatus::create(['id' => $command->getId()]));
        $status = $this->getRepo()->getRefdataReference($previousStatus['status']);
        $licence->setStatus($status);

        $this->getRepo('Licence')->save($licence);

        $this->result->addMessage('Licence ' . $licence->getId() . ' surrender withdrawn');

        return $this->result;
    }
}

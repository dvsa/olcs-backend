<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

class UpdateFeeStatus extends AbstractCommandHandler
{

    protected $repoServiceName = 'Fee';

    /**
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $status = $command->getStatus();

        $fee = $this->getRepo()->fetchById($command->getId());
        $fee->setStatus($this->getRepo()->getRefdataReference($status));
        $this->getRepo()->save($fee);

        $this->result
            ->addId('fee', $fee->getId())
            ->addMessage('Fee status updated:' . $status);

        return $this->result;
    }
}
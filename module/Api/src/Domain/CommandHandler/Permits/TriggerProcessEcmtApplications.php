<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Trigger process ECMT applications
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class TriggerProcessEcmtApplications extends AbstractCommandHandler
{
    use QueueAwareTrait;

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $result->addMessage('Queuing processing of ECMT applications');
        $processCmd = $this->createQueue(null, Queue::TYPE_PROCESS_ECMT_APPLICATIONS, []);
        $result->merge(
            $this->handleSideEffect($processCmd)
        );

        return $result;
    }
}


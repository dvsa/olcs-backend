<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\QueueAcceptScoring as QueueAcceptScoringCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Queue accept scoring
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class QueueAcceptScoring extends AbstractCommandHandler
{
    use QueueAwareTrait;

    protected $repoServiceName = 'IrhpPermitStock';

    /**
     * Handle command
     *
     * @param QueueAcceptScoringCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $stockId = $command->getId();

        $this->getRepo('IrhpPermitStock')->updateStatus($stockId, IrhpPermitStock::STATUS_ACCEPT_PENDING);

        $this->result->merge(
            $this->handleSideEffect(
                $this->createQueue($stockId, Queue::TYPE_ACCEPT_ECMT_SCORING, [])
            )
        );

        $this->result->addMessage('Queueing accept scoring of ECMT applications');
        return $this->result;
    }
}

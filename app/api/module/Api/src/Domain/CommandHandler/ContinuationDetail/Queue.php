<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueueCmd;

/**
 * Queue letters
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Queue extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ContinuationDetail';

    public function handleCommand(CommandInterface $command)
    {
        $ids = $command->getIds();
        $result = new Result();
        foreach ($ids as $continuationDetailId) {
            $createCmd = CreateQueueCmd::create(
                [
                    'entityId' => $continuationDetailId,
                    'type' => $command->getType(),
                    'status' => QueueEntity::STATUS_QUEUED
                ]
            );
            $result->merge($this->handleSideEffect($createCmd));
        }

        $result->addMessage('All letters queued');

        return $result;
    }
}

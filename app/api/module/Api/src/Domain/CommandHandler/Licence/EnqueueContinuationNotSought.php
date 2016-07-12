<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler as DomainAbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Queue\Queue;

/**
 * Enqueue licence CNS jobs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class EnqueueContinuationNotSought extends DomainAbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Queue';

    /**
     * Handle command
     *
     * @param EnqueueContinuationNotSought $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $licences = $command->getLicences();
        $rows = $this->getRepo()->enqueueContinuationNotSought($command->getLicences());
        $this->result->addMessage('Enqueued ' . $rows . ' CNS messages');

        $queue = new Queue();
        $queue->setStatus($this->getRepo()->getRefdataReference(Queue::STATUS_QUEUED));
        $queue->setType($this->getRepo()->getRefdataReference(Queue::TYPE_CNS_EMAIL));
        $options = [
            'licences' => $licences,
            'date' => $command->getDate()
        ];
        $queue->setOptions(json_encode($options));
        $this->getRepo()->save($queue);

        $this->result->addMessage('Send CNS email message enqueued');

        return $this->result;
    }
}

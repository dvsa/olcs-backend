<?php

/**
 * Create Queue item
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Queue;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Create Queue item
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler
{
    protected $repoServiceName = 'Queue';

    public function handleCommand(CommandInterface $query)
    {
        $queue = new QueueEntity();

        $queue->validateQueue($query->getType(), $query->getStatus());

        $queue->setType($this->getRepo()->getRefdataReference($query->getType()));
        $queue->setStatus($this->getRepo()->getRefdataReference($query->getStatus()));
        $queue->setEntityId($query->getEntityId());

        $this->getRepo()->save($queue);

        $result = new Result();
        $result->addId('queue' . $queue->getId(), $queue->getId());
        $result->addMessage('Queue created');
        return $result;
    }
}

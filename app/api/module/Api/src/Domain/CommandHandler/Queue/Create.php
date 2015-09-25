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
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;

/**
 * Create Queue item
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler
{
    protected $repoServiceName = 'Queue';

    public function handleCommand(CommandInterface $command)
    {
        $queue = new QueueEntity();
        $queue->validateQueue($command->getType(), $command->getStatus());
        $queue->setType(
            $this->getRepo()->getRefdataReference($command->getType())
        );
        $queue->setStatus(
            $this->getRepo()->getRefdataReference($command->getStatus())
        );
        $queue->setEntityId($command->getEntityId());
        if ($command->getUser()) {
            $queue->setCreatedBy($this->getRepo()->getReference(UserEntity::class, $command->getUser()));
        }
        $this->getRepo()->save($queue);

        $result = new Result();
        $result->addId('queue' . $queue->getId(), $queue->getId());
        $result->addMessage('Queue created');
        return $result;
    }
}

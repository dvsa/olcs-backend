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
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateCmd;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Create Queue item
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Queue';

    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var CreateCmd $command
         */
        $queue = new QueueEntity();

        $queue->validateQueue($command->getType(), $command->getStatus(), $command->getProcessAfterDate());

        $queue->setType(
            $this->getRepo()->getRefdataReference($command->getType())
        );

        $queue->setStatus(
            $this->getRepo()->getRefdataReference($command->getStatus())
        );

        $queue->setEntityId($command->getEntityId());

        // @TODO: should be removed after OLCS-12253 will be done, createdBy will be populating automatically
        $queue->setCreatedBy($this->getRepo()->getReference(UserEntity::class, $this->getCurrentUser()->getId()));

        if ($command->getOptions()) {
            $queue->setOptions($command->getOptions());
        }

        //using not empty as potentially we could end up with empty string instead of null
        //date has been validated using validateQueue above
        if (!empty($command->getProcessAfterDate())) {
            $processAfterDate = new \DateTime($command->getProcessAfterDate());
            $queue->setProcessAfterDate($processAfterDate);
        }

        $this->getRepo()->save($queue);

        $result = new Result();
        $result->addId('queue', $queue->getId(), true);
        $result->addMessage('Queue created');
        return $result;
    }
}

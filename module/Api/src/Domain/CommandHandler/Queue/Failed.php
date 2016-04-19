<?php

/**
 * Failed queue item handler
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Queue;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Command\Queue\Delete as DeleteQueueCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Failed as FailedQueueCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class Failed extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Queue';

    /**
     * @param CommandInterface|FailedQueueCmd $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $item = $command->getItem();
        $item->setStatus($this->getRepo()->getRefdataReference(QueueEntity::STATUS_FAILED));
        $this->getRepo()->save($item);

        $result = new Result();
        $result
            ->addId('queue', $item->getId())
            ->addMessage('Queue item marked failed');

        $result->merge($this->handleSideEffect(DeleteQueueCmd::create(['id' => $item->getId()])));

        return $result;
    }
}

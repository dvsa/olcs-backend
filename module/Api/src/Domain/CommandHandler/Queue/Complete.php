<?php

/**
 * Complete queue item handler
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Queue;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class Complete extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Queue';

    /**
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $item = $command->getItem();
        $item->setStatus($this->getRepo()->getRefdataReference(QueueEntity::STATUS_COMPLETE));
        $this->getRepo()->save($item);

        $result = new Result();
        $result
            ->addId('queue', $item->getId())
            ->addMessage('Queue item marked complete');

        return $result;
    }
}

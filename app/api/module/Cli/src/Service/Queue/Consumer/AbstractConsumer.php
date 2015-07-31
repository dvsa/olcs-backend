<?php

/**
 * Abstract Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\MessageConsumerInterface;
use Dvsa\Olcs\Api\Domain\Command\Queue\Complete as CompleteCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Failed as FailedCmd;

/**
 * Abstract Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractConsumer implements MessageConsumerInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Process the message item
     *
     * @param QueueEntity $item
     * @return string
     */
    abstract public function processMessage(QueueEntity $item);

    /**
     * Called when processing the message was successful
     *
     * @param QueueEntity $item
     * @return string
     */
    protected function success(QueueEntity $item, $message = null)
    {
        $command = CompleteCmd::create(['item' => $item]);
        $this->getServiceLocator()->get('CommandHandlerManager')
            ->handleCommand($command);

        return 'Successfully processed message: '
            . $item->getId() . ' ' . $item->getOptions()
            . ($message ? ' ' . $message : '');
    }

    /**
     * Mark the message as failed
     *
     * @param QueueEntity $item
     * @param string $reason
     * @return string
     */
    protected function failed(QueueEntity $item, $reason = null)
    {
        $command = FailedCmd::create(['item' => $item]);
        $this->getServiceLocator()->get('CommandHandlerManager')
            ->handleCommand($command);

        return 'Failed to process message: '
            . $item->getId() . ' ' . $item->getOptions()
            . ' ' .  $reason;
    }
}

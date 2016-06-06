<?php

/**
 * Abstract Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Command\Queue\Complete as CompleteCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Failed as FailedCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Retry as RetryCmd;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Olcs\Logging\Log\Logger;

/**
 * Abstract Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractConsumer implements MessageConsumerInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

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

        $description = 'Successfully processed message';
        $content = $item->getId() . ' ' . $item->getOptions() . ($message ? ' ' . $message : '');

        Logger::log(
            \Zend\Log\Logger::DEBUG,
            $description,
            ['errorLevel' => 0, 'content' => $content]
        );

        return $description . ': ' . $content;
    }

    /**
     * Mark the message as failed
     *
     * @param QueueEntity $item
     * @param string $reason
     * @return string
     */
    public function failed(QueueEntity $item, $reason = null)
    {
        $command = FailedCmd::create(['item' => $item]);
        $this->getServiceLocator()->get('CommandHandlerManager')
            ->handleCommand($command);

        $description = 'Failed to process message';
        $content = $item->getId() . ' ' . $item->getOptions() . ' ' .  $reason;

        Logger::log(
            \Zend\Log\Logger::ERR,
            $description,
            ['errorLevel' => 1, 'content' => $content]
        );

        return $description . ': ' . $content;
    }

    /**
     * Requeue the message
     *
     * @param QueueEntity $item
     * @param string $retryAfter (seconds)
     * @return string
     */
    protected function retry(QueueEntity $item, $retryAfter)
    {
        $command = RetryCmd::create(['item' => $item, 'retryAfter' => $retryAfter]);
        $this->getServiceLocator()->get('CommandHandlerManager')
            ->handleCommand($command);

        $description = 'Requeued message';
        $content = $item->getId() . ' ' . $item->getOptions() . ' for retry in ' .  $retryAfter;

        Logger::log(
            \Zend\Log\Logger::WARN,
            $description,
            ['errorLevel' => 0, 'content' => $content]
        );

        return $description . ': ' . $content;
    }
}

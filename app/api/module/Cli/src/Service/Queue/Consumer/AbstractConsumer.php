<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Command\Queue\Complete as CompleteCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Failed as FailedCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Retry as RetryCmd;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

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
     * @param QueueEntity $item    queue item
     * @param string|null $message success message
     *
     * @return string
     */
    protected function success(QueueEntity $item, $message = null)
    {
        $command = CompleteCmd::create(['item' => $item]);
        $this->handleSideEffectCommand($command);

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
     * @param QueueEntity $item   queue item
     * @param string      $reason exception message passed from the command handler
     *
     * @return string
     */
    public function failed(QueueEntity $item, $reason = null)
    {
        $command = FailedCmd::create(
            [
                'item' => $item,
                'lastError' => $reason,
            ]
        );
        $this->handleSideEffectCommand($command);

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
     * @param QueueEntity $item       queue item
     * @param string      $retryAfter (seconds)
     * @param string|null $reason     exception message passed from the command handler
     *
     * @return string
     */
    protected function retry(QueueEntity $item, $retryAfter, $reason = null)
    {
        $command = RetryCmd::create(['item' => $item, 'retryAfter' => $retryAfter, 'lastError' => $reason]);
        $this->handleSideEffectCommand($command);

        $description = 'Requeued message';
        $content = $item->getId() . ' ' . $item->getOptions() . ' for retry in ' .  $retryAfter . ' ' .  $reason;

        Logger::log(
            \Zend\Log\Logger::WARN,
            $description,
            ['errorLevel' => 0, 'content' => $content]
        );

        return $description . ': ' . $content;
    }

    /**
     * Run a DTO command side effect
     *
     * @param \Dvsa\Olcs\Transfer\Command\CommandInterface $command the command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function handleSideEffectCommand(\Dvsa\Olcs\Transfer\Command\CommandInterface $command)
    {
        return $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($command, false);
    }

    /**
     * Run a DTO command
     *
     * @param \Dvsa\Olcs\Transfer\Command\CommandInterface $command the command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function handleCommand(\Dvsa\Olcs\Transfer\Command\CommandInterface $command)
    {
        return $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($command);
    }
}

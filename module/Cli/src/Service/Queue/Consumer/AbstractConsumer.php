<?php

/**
 * Abstract Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Command\Queue\Complete as CompleteCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Failed as FailedCmd;
use Dvsa\Olcs\Api\Domain\Exception\Exception as DomainException;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
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
     * @var string the command to handle processing
     */
    protected $commandName = 'override_me';

    /**
     * @param QueueEntity $item
     * @return array
     */
    abstract public function getCommandData(QueueEntity $item);

    /**
     * Process the message item
     *
     * @param QueueEntity $item
     * @return string
     */
    public function processMessage(QueueEntity $item)
    {
        $commandClass = $this->commandName;
        $commandData = $this->getCommandData($item);
        $command = $commandClass::create($commandData);

        try {
            $result = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($command);
        } catch (DomainException $e) {
            $message = !empty($e->getMessages()) ? implode(', ', $e->getMessages()) : $e->getMessage();
            return $this->failed($item, $message);
        } catch (\Exception $e) {
            return $this->failed($item, $e->getMessage());
        }

        $message = null;
        if (!empty($result->getMessages())) {
            $message = implode(', ', $result->getMessages());
        }
        return $this->success($item, $message);
    }

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

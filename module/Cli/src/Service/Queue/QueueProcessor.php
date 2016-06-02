<?php

/**
 * Queue Processor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from olcs-internal Cli\Service\Queue
 */
namespace Dvsa\Olcs\Cli\Service\Queue;

use Dvsa\Olcs\Api\Domain\Query\Queue\NextItem as NextQueueItemQry;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\MessageConsumerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Queue Processor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from olcs-internal Cli\Service\Queue
 */
class QueueProcessor implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Process next item
     *
     * @param string $type
     * @return string
     */
    public function processNextItem($type = null)
    {
        $item = $this->getNextItem($type);

        if ($item === null) {
            return null;
        }

        return $this->processMessage($item);
    }

    /**
     * Process message
     *
     * @param QueueEntity $item
     * @return string
     */
    protected function processMessage($item)
    {
        $consumer = $this->getMessageConsumer($item);

        try {
            return $consumer->processMessage($item);
        } catch (\Exception $e) {
            // mark the item as failed
            return $consumer->failed($item, $e->getMessage());
        }
    }

    /**
     * Grab the next message in the queue
     *
     * @param string $type
     * @return QueueEntity|null
     */
    protected function getNextItem($type = null)
    {
        $query = NextQueueItemQry::create(['type' => $type]);
        return $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($query);
    }

    /**
     * Get message consumer
     *
     * @param QueueEntity $item
     * @return MessageConsumerInterface
     */
    protected function getMessageConsumer($item)
    {
        return $this->getServiceLocator()->get('MessageConsumerManager')
            ->get($item->getType()->getId());
    }
}

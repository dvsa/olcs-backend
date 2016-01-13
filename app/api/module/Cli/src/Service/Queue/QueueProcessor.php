<?php

/**
 * Queue Processor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from olcs-internal Cli\Service\Queue
 */
namespace Dvsa\Olcs\Cli\Service\Queue;

use Dvsa\Olcs\Api\Domain\Query\Queue\NextItem as NextQueueItemQry;
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

    public function processNextItem($type = null)
    {
        $item = $this->getNextItem($type);

        if ($item === null) {
            return null;
        }

        return $this->processMessage($item);
    }

    protected function processMessage($item)
    {
        $consumer = $this->getMessageConsumer($item);
        return $consumer->processMessage($item);
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

    protected function getMessageConsumer($item)
    {
        return $this->getServiceLocator()->get('MessageConsumerManager')
            ->get($item->getType()->getId());
    }
}

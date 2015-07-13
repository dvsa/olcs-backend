<?php

/**
 * Abstract Companies House Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\MessageConsumerInterface;

/**
 * Abstract Companies House Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractConsumer implements MessageConsumerInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Process the message item
     *
     * @param array $item
     * @return boolean
     */
    public function processMessage(array $item)
    {
        $options = (array) json_decode($item['options']);

        throw new \Exception('@todo ' . __METHOD__);
    }

    /**
     * Called when processing the message was successful
     *
     * @param array $item
     * @return string
     */
    protected function success(array $item, $message = null)
    {
        throw new \Exception('@todo success command');
        $this->getServiceLocator()->get('Entity\Queue')->complete($item);

        return 'Successfully processed message: '
            . $item['id'] . ' ' . $item['options']
            . ($message ? ' ' . $message : '');
    }

    /**
     * Mark the message as failed
     *
     * @param array $item
     * @param string $reason
     * @return string
     */
    protected function failed(array $item, $reason = null)
    {
        throw new \Exception('@todo failed command');
        $this->getServiceLocator()->get('Entity\Queue')->failed($item);

        return 'Failed to process message: '
            . $item['id'] . ' ' . $item['options']
            . ' ' .  $reason;
    }
}

<?php

/**
 * Abstract Companies House Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
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
     * @var string the command to handle processing
     */
    protected $commandName = 'override_me';

    /**
     * Process the message item
     *
     * @param QueueEntity $item
     * @return boolean
     */
    public function processMessage(QueueEntity $item)
    {
        $options = (array) json_decode($item->getOptions());

        $commandClass = $this->commandName;
        $command = $commandClass::create(['companyNumber' => $options['companyNumber']]);
        $response = $this->getServiceLocator()->get('CommandHandlerManager')
            ->handleCommand($command);
var_dump($response); exit;
        if (!$response->isOk()) {
            return $this->failed($item, $response->getMessage());
        }

        return $this->success($item, $response->getMessage());
    }

    /**
     * Called when processing the message was successful
     *
     * @param QueueEntity $item
     * @return string
     */
    protected function success(QueueEntity $item, $message = null)
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
     * @param QueueEntity $item
     * @param string $reason
     * @return string
     */
    protected function failed(QueueEntity $item, $reason = null)
    {
        throw new \Exception('@todo failed command');
        $this->getServiceLocator()->get('Entity\Queue')->failed($item);

        return 'Failed to process message: '
            . $item['id'] . ' ' . $item['options']
            . ' ' .  $reason;
    }
}

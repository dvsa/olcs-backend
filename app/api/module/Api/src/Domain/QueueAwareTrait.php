<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueue;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Zend\Json\Json as ZendJson;

/**
 * Queue Aware
 */
trait QueueAwareTrait
{
    /**
     * Adds an email to the queue
     *
     * @param string $cmdClass
     * @param array $cmdData
     * @param int $entityId
     * @param string|null $processAfterDate
     * @return CreateQueue
     */
    public function emailQueue($cmdClass, array $cmdData, $entityId, $processAfterDate = null)
    {
        $options =                     [
            'commandClass' => $cmdClass,
            'commandData' => $cmdData,
        ];

        return $this->createQueue($entityId, Queue::TYPE_EMAIL, $options, $processAfterDate);
    }

    /**
     * Creates a queue job
     *
     * @param int $entityId
     * @param string $type
     * @param array $options
     * @param string|null $processAfterDate
     * @return static
     */
    public function createQueue($entityId, $type, array $options, $processAfterDate = null)
    {
        return CreateQueue::create(
            [
                'entityId' => $entityId,
                'type' => $type,
                'status' => Queue::STATUS_QUEUED,
                'options' => ZendJson::encode($options),
                'processAfterDate' => $processAfterDate
            ]
        );
    }
}

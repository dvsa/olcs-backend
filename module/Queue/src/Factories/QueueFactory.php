<?php

namespace Dvsa\Olcs\Queue\Factories;

use Aws\Sqs\SqsClient;
use Dvsa\Olcs\Queue\Service\Queue;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class QueueFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var $sqsClient SqsClient
         */
        $sqsClient = $serviceLocator->get('SqsClient');
        $queueService = new Queue($sqsClient);

        return $queueService;
    }
}

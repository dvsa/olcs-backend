<?php

namespace Dvsa\Olcs\Queue\Factories;

use Aws\Sqs\SqsClient;
use Dvsa\Olcs\Queue\Service\Queue;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class QueueFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Queue
    {
        return $this->__invoke($serviceLocator, Queue::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Queue
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Queue
    {
        /**
         * @var $sqsClient SqsClient
         */
        $sqsClient = $container->get('SqsClient');
        $queueService = new Queue($sqsClient);
        return $queueService;
    }
}

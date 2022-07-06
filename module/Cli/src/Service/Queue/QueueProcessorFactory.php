<?php

namespace Dvsa\Olcs\Cli\Service\Queue;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class QueueProcessorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new QueueProcessor(
            $container->get('QueryHandlerManager'),
            $container->get('MessageConsumerManager')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return QueueProcessor
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, QueueProcessor::class);
    }
}

<?php

namespace Dvsa\Olcs\Cli\Service\Queue;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class QueueProcessorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new QueueProcessor(
            $container->get('QueryHandlerManager'),
            $container->get('MessageConsumerManager')
        );
    }
}

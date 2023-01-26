<?php

namespace Dvsa\Olcs\Queue\Factories;

use Dvsa\Olcs\Queue\Service\Message\MessageBuilder;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class MessageBuilderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator): MessageBuilder
    {
        return $this->__invoke($serviceLocator, MessageBuilder::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return MessageBuilder
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MessageBuilder
    {
        return new MessageBuilder();
    }
}

<?php

namespace Dvsa\Olcs\Queue\Factories;

use Dvsa\Olcs\Queue\Service\Message\MessageBuilder;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class MessageBuilderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new MessageBuilder();
    }
}

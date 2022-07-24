<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory;

use Dvsa\Olcs\Api\Service\Traits\GenericFactoryCreateServiceTrait;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumerServices;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;

class GenericFactory implements FactoryInterface
{
    use GenericFactoryCreateServiceTrait;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName(
            $container->getServiceLocator()->get(AbstractConsumerServices::class),
        );
    }
}

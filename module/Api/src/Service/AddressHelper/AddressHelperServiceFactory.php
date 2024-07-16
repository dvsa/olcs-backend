<?php

namespace Dvsa\Olcs\Api\Service\AddressHelper;

use Dvsa\Olcs\Api\Domain\Repository;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class AddressHelperServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AddressHelperService
    {
        return new AddressHelperService(
            $container->get('AddressService'),
            $container->get(Repository\PostcodeEnforcementArea::class),
            $container->get(Repository\AdminAreaTrafficArea::class)
        );
    }
}

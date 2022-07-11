<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class AbstractReviewServiceServicesFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AbstractReviewServiceServices(
            $container->get('translator')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return AbstractReviewServiceServices
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, AbstractReviewServiceServices::class);
    }
}

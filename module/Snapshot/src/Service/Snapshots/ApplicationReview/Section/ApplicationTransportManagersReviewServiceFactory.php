<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationTransportManagersReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ApplicationTransportManagersReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\TransportManagers')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return ApplicationTransportManagersReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, ApplicationTransportManagersReviewService::class);
    }
}

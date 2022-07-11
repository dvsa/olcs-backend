<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationTransportManagersReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VariationTransportManagersReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\TransportManagers')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return VariationTransportManagersReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, VariationTransportManagersReviewService::class);
    }
}

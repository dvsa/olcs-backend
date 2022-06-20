<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationVehiclesPsvReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VariationVehiclesPsvReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\VehiclesPsv')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return VariationVehiclesPsvReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, VariationVehiclesPsvReviewService::class);
    }
}

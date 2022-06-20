<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationVehiclesPsvReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ApplicationVehiclesPsvReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\VehiclesPsv')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return ApplicationVehiclesPsvReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, ApplicationVehiclesPsvReviewService::class);
    }
}

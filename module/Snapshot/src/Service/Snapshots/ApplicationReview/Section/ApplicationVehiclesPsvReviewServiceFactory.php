<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ApplicationVehiclesPsvReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ApplicationVehiclesPsvReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\VehiclesPsv')
        );
    }
}

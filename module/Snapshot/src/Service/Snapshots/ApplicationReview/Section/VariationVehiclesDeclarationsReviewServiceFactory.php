<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationVehiclesDeclarationsReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VariationVehiclesDeclarationsReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\ApplicationVehiclesDeclarations')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return VariationVehiclesDeclarationsReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, VariationVehiclesDeclarationsReviewService::class);
    }
}

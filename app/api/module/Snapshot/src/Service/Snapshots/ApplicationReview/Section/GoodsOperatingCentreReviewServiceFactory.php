<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GoodsOperatingCentreReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new GoodsOperatingCentreReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\PsvOperatingCentre')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return GoodsOperatingCentreReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, GoodsOperatingCentreReviewService::class);
    }
}

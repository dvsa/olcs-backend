<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationOperatingCentresReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ApplicationOperatingCentresReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\PsvOperatingCentre'),
            $container->get('Review\ApplicationPsvOcTotalAuth'),
            $container->get('Review\GoodsOperatingCentre'),
            $container->get('Review\ApplicationGoodsOcTotalAuth'),
            $container->get('Review\TrafficArea')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return ApplicationOperatingCentresReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, ApplicationOperatingCentresReviewService::class);
    }
}

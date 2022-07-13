<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationOperatingCentresReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VariationOperatingCentresReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\PsvOperatingCentre'),
            $container->get('Review\VariationPsvOcTotalAuth'),
            $container->get('Review\GoodsOperatingCentre'),
            $container->get('Review\VariationGoodsOcTotalAuth'),
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return VariationOperatingCentresReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, VariationOperatingCentresReviewService::class);
    }
}

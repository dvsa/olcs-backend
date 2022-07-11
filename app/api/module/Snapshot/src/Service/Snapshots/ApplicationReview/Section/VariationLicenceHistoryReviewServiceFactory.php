<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationLicenceHistoryReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VariationLicenceHistoryReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\ApplicationLicenceHistory')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return VariationLicenceHistoryReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, VariationLicenceHistoryReviewService::class);
    }
}

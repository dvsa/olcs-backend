<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationFinancialHistoryReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VariationFinancialHistoryReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\ApplicationFinancialHistory')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return VariationFinancialHistoryReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, VariationFinancialHistoryReviewService::class);
    }
}

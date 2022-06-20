<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationFinancialEvidenceReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VariationFinancialEvidenceReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\ApplicationFinancialEvidence')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return VariationFinancialEvidenceReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, VariationFinancialEvidenceReviewService::class);
    }
}

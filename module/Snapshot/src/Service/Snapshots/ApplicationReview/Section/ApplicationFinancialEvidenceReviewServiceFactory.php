<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationFinancialEvidenceReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ApplicationFinancialEvidenceReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('QueryHandlerManager')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return ApplicationFinancialEvidenceReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, ApplicationFinancialEvidenceReviewService::class);
    }
}

<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FinanceReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FinanceReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('FinancialStandingHelperService'),
            $container->get('RepositoryServiceManager')->get('Document')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return FinanceReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, FinanceReviewService::class);
    }
}

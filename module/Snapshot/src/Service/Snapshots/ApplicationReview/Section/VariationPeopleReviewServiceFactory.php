<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationPeopleReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VariationPeopleReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\People')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return VariationPeopleReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, VariationPeopleReviewService::class);
    }
}

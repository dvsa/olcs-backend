<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationConvictionsPenaltiesReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VariationConvictionsPenaltiesReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\ApplicationConvictionsPenalties')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return VariationConvictionsPenaltiesReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, VariationConvictionsPenaltiesReviewService::class);
    }
}

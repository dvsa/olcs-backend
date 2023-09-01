<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ApplicationConditionsUndertakingsReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ApplicationConditionsUndertakingsReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\ConditionsUndertakings')
        );
    }
}

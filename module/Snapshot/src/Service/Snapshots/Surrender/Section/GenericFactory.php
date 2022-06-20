<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Snapshot\Service\Snapshots\GenericFactoryCreateServiceTrait;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;

class GenericFactory implements FactoryInterface
{
    use GenericFactoryCreateServiceTrait;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName(
            $container->get(AbstractReviewServiceServices::class),
        );
    }
}

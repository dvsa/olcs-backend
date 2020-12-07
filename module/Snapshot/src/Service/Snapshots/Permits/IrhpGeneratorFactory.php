<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Permits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IrhpGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new IrhpGenerator(
            $serviceLocator->get('PermitsAnswersSummaryGenerator')
        );
    }
}

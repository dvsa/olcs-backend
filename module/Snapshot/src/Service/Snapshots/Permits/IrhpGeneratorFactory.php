<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Permits;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

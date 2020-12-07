<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class PermitStartDateGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitStartDateGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PermitStartDateGenerator(
            $serviceLocator->get('QaCommonDateWithThresholdElementGenerator')
        );
    }
}

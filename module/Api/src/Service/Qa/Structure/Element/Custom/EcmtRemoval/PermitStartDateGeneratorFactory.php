<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('QaEcmtRemovalPermitStartDateElementFactory'),
            $serviceLocator->get('QaCommonCurrentDateTimeFactory'),
            $serviceLocator->get('QaCommonDateIntervalFactory'),
            $serviceLocator->get('QaDateElementGenerator')
        );
    }
}

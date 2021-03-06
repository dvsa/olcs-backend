<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\CertRoadworthiness;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class MotExpiryDateGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return MotExpiryDateGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new MotExpiryDateGenerator(
            $serviceLocator->get('QaCertRoadworthinessMotExpiryDateElementFactory'),
            $serviceLocator->get('QaCommonDateWithThresholdElementGenerator')
        );
    }
}

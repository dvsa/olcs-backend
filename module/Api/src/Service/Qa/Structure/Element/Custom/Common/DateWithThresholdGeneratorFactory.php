<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DateWithThresholdGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DateWithThresholdGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DateWithThresholdGenerator(
            $serviceLocator->get('QaCommonDateWithThresholdElementFactory'),
            $serviceLocator->get('CommonCurrentDateTimeFactory'),
            $serviceLocator->get('QaCommonDateIntervalFactory'),
            $serviceLocator->get('QaDateElementGenerator')
        );
    }
}

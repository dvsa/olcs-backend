<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('QaCommonCurrentDateTimeFactory'),
            $serviceLocator->get('QaCommonDateIntervalFactory'),
            $serviceLocator->get('QaDateElementGenerator')
        );
    }
}

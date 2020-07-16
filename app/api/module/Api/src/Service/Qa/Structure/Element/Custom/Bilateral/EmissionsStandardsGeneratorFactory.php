<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmissionsStandardsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsStandardsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new EmissionsStandardsGenerator(
            $serviceLocator->get('QaBilateralEmissionsStandardsElementFactory')
        );
    }
}

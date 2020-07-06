<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ThirdCountryGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ThirdCountryGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ThirdCountryGenerator(
            $serviceLocator->get('QaBilateralThirdCountryElementFactory')
        );
    }
}

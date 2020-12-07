<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsMoroccoGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsMoroccoGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsMoroccoGenerator(
            $serviceLocator->get('QaBilateralNoOfPermitsMoroccoElementFactory')
        );
    }
}

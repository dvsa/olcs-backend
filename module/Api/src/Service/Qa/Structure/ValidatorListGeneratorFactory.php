<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ValidatorListGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ValidatorListGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ValidatorListGenerator(
            $serviceLocator->get('QaValidatorListFactory'),
            $serviceLocator->get('QaValidatorGenerator')
        );
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ValidatorGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ValidatorGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ValidatorGenerator(
            $serviceLocator->get('QaValidatorFactory')
        );
    }
}

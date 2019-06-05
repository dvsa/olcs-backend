<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

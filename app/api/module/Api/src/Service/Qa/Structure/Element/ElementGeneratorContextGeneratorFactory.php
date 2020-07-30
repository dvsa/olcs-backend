<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ElementGeneratorContextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ElementGeneratorContextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ElementGeneratorContextGenerator(
            $serviceLocator->get('QaValidatorListGenerator'),
            $serviceLocator->get('QaElementGeneratorContextFactory')
        );
    }
}

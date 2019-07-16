<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OptionsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OptionsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $optionsGenerator = new OptionsGenerator();

        $optionsGenerator->registerSource('refData', $serviceLocator->get('QaRefDataOptionsSource'));

        return $optionsGenerator;
    }
}

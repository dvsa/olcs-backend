<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OptionsGeneratorProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OptionsGeneratorProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mappings = [
            'direct' => $serviceLocator->get('QaDirectOptionsGenerator'),
        ];

        return new OptionsGeneratorProvider($mappings);
    }
}

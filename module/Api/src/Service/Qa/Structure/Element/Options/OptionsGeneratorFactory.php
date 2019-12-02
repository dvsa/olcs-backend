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
        $optionsGenerator = new OptionsGenerator(
            $serviceLocator->get('QaOptionListFactory'),
            $serviceLocator->get('QaOptionFactory')
        );

        $optionsGenerator->registerSource('refData', $serviceLocator->get('QaRefDataOptionsSource'));
        $optionsGenerator->registerSource('repoQuery', $serviceLocator->get('QaRepoQueryOptionsSource'));

        return $optionsGenerator;
    }
}

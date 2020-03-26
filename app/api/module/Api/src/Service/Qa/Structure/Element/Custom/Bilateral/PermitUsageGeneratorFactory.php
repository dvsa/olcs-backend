<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermitUsageGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PermitUsageGenerator(
            $serviceLocator->get('QaRadioElementFactory'),
            $serviceLocator->get('QaTranslateableTextGenerator'),
            $serviceLocator->get('QaOptionFactory'),
            $serviceLocator->get('QaOptionListFactory')
        );
    }
}

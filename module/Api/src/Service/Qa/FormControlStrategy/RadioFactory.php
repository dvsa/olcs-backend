<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RadioFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Radio
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Radio(
            $serviceLocator->get('QaOptionsGeneratorProvider'),
            $serviceLocator->get('QaGenericAnswerSaver')
        );
    }
}

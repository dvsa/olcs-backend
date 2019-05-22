<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CheckboxFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Checkbox
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Checkbox(
            $serviceLocator->get('QaGenericAnswerSaver')
        );
    }
}

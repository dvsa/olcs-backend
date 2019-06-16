<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Checkbox;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CheckboxAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CheckboxAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CheckboxAnswerSaver(
            $serviceLocator->get('QaGenericAnswerWriter'),
            $serviceLocator->get('QaGenericAnswerFetcher')
        );
    }
}

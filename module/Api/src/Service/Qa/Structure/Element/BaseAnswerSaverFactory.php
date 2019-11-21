<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BaseAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BaseAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new BaseAnswerSaver(
            $serviceLocator->get('QaGenericAnswerWriter'),
            $serviceLocator->get('QaGenericAnswerFetcher')
        );
    }
}

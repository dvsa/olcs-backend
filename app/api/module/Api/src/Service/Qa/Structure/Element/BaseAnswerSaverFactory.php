<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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

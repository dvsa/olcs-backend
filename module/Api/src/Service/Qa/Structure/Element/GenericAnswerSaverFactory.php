<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GenericAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GenericAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new GenericAnswerSaver(
            $serviceLocator->get('QaBaseAnswerSaver')
        );
    }
}

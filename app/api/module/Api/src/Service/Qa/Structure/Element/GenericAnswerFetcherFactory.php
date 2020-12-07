<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GenericAnswerFetcherFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GenericAnswerFetcher
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new GenericAnswerFetcher(
            $serviceLocator->get('QaNamedAnswerFetcher')
        );
    }
}

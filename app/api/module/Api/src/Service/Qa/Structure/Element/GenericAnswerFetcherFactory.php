<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GenericAnswerClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GenericAnswerClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new GenericAnswerClearer(
            $serviceLocator->get('QaGenericAnswerProvider'),
            $serviceLocator->get('RepositoryServiceManager')->get('Answer')
        );
    }
}

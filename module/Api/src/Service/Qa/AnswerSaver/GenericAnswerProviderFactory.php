<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GenericAnswerProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GenericAnswerProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new GenericAnswerProvider(
            $serviceLocator->get('RepositoryServiceManager')->get('Answer')
        );
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GenericAnswerWriterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GenericAnswerWriter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new GenericAnswerWriter(
            $serviceLocator->get('QaGenericAnswerProvider'),
            $serviceLocator->get('QaAnswerFactory'),
            $serviceLocator->get('RepositoryServiceManager')->get('Answer')
        );
    }
}

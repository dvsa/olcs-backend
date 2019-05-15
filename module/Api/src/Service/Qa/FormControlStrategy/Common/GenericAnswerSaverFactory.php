<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('RepositoryServiceManager')->get('Answer')
        );
    }
}

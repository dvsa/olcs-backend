<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TextFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Text
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Text(
            $serviceLocator->get('QaGenericAnswerSaver')
        );
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermitUsageAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PermitUsageAnswerSaver(
            $serviceLocator->get('QaGenericAnswerFetcher'),
            $serviceLocator->get('QaApplicationAnswersClearer'),
            $serviceLocator->get('QaGenericAnswerSaver')
        );
    }
}

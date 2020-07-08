<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmissionsStandardsAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsStandardsAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new EmissionsStandardsAnswerSaver(
            $serviceLocator->get('QaBilateralCountryDeletingAnswerSaver')
        );
    }
}

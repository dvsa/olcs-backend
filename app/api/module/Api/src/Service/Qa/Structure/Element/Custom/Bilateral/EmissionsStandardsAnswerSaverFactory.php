<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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

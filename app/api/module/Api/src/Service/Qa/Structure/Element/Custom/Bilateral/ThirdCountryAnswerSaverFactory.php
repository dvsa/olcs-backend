<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ThirdCountryAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ThirdCountryAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ThirdCountryAnswerSaver(
            $serviceLocator->get('QaBilateralCountryDeletingAnswerSaver')
        );
    }
}

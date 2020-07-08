<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CabotageOnlyAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CabotageOnlyAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CabotageOnlyAnswerSaver(
            $serviceLocator->get('QaBilateralCountryDeletingAnswerSaver')
        );
    }
}

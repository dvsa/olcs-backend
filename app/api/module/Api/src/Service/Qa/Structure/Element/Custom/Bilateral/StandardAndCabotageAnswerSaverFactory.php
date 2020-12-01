<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StandardAndCabotageAnswerSaver(
            $serviceLocator->get('QaNamedAnswerFetcher'),
            $serviceLocator->get('PermitsBilateralCommonStandardAndCabotageUpdater')
        );
    }
}

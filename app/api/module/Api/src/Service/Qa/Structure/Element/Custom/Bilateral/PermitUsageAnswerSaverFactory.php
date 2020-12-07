<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('PermitsBilateralCommonPermitUsageUpdater')
        );
    }
}

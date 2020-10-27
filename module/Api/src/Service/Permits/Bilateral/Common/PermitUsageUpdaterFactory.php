<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermitUsageUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PermitUsageUpdater(
            $serviceLocator->get('QaGenericAnswerWriter'),
            $serviceLocator->get('QaApplicationAnswersClearer')
        );
    }
}

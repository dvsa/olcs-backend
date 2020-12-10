<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FeeUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeeUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FeeUpdater(
            $serviceLocator->get('CqrsCommandCreator'),
            $serviceLocator->get('CommandHandlerManager'),
            $serviceLocator->get('PermitsFeesEcmtApplicationFeeCommandCreator')
        );
    }
}

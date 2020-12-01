<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StandardAndCabotageUpdater(
            $serviceLocator->get('PermitsBilateralCommonModifiedAnswerUpdater')
        );
    }
}

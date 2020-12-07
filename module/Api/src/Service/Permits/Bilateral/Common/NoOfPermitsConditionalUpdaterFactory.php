<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsConditionalUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsConditionalUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsConditionalUpdater(
            $serviceLocator->get('PermitsBilateralCommonNoOfPermitsUpdater')
        );
    }
}

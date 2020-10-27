<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

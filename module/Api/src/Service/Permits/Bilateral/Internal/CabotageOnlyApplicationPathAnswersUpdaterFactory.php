<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CabotageOnlyApplicationPathAnswersUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CabotageOnlyApplicationPathAnswersUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CabotageOnlyApplicationPathAnswersUpdater(
            $serviceLocator->get('PermitsBilateralInternalGenericAnswerUpdater')
        );
    }
}

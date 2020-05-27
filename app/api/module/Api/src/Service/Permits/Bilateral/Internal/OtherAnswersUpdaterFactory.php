<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OtherAnswersUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OtherAnswersUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new OtherAnswersUpdater(
            $serviceLocator->get('PermitsBilateralInternalPermitUsageAnswerUpdater'),
            $serviceLocator->get('PermitsBilateralInternalCabotageAnswerUpdater')
        );
    }
}

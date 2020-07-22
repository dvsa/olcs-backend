<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageApplicationPathAnswersUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageApplicationPathAnswersUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StandardAndCabotageApplicationPathAnswersUpdater(
            $serviceLocator->get('PermitsBilateralInternalGenericAnswerUpdater')
        );
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TurkeyApplicationPathAnswersUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TurkeyApplicationPathAnswersUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TurkeyApplicationPathAnswersUpdater(
            $serviceLocator->get('PermitsBilateralInternalGenericAnswerUpdater')
        );
    }
}

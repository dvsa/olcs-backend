<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UkraineApplicationPathAnswersUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UkraineApplicationPathAnswersUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new UkraineApplicationPathAnswersUpdater(
            $serviceLocator->get('PermitsBilateralInternalGenericAnswerUpdater')
        );
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NumberOfPermitsQuestionHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NumberOfPermitsQuestionHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NumberOfPermitsQuestionHandler(
            $serviceLocator->get('PermitsBilateralInternalPermitUsageSelectionGenerator'),
            $serviceLocator->get('PermitsBilateralInternalBilateralRequiredGenerator'),
            $serviceLocator->get('PermitsBilateralCommonNoOfPermitsConditionalUpdater')
        );
    }
}

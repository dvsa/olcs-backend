<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageQuestionHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageQuestionHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StandardAndCabotageQuestionHandler(
            $serviceLocator->get('PermitsBilateralInternalPermitUsageSelectionGenerator'),
            $serviceLocator->get('PermitsBilateralInternalBilateralRequiredGenerator'),
            $serviceLocator->get('PermitsBilateralCommonStandardAndCabotageUpdater')
        );
    }
}

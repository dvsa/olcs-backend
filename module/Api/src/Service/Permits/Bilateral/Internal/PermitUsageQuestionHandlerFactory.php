<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermitUsageQuestionHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageQuestionHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PermitUsageQuestionHandler(
            $serviceLocator->get('PermitsBilateralInternalPermitUsageSelectionGenerator'),
            $serviceLocator->get('PermitsBilateralCommonPermitUsageUpdater')
        );
    }
}

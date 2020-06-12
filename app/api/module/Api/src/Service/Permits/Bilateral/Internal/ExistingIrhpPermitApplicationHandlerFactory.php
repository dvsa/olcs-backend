<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExistingIrhpPermitApplicationHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ExistingIrhpPermitApplicationHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new ExistingIrhpPermitApplicationHandler(
            $repoServiceManager->get('IrhpPermitApplication'),
            $repoServiceManager->get('IrhpPermitStock'),
            $serviceLocator->get('PermitsBilateralInternalPermitUsageSelectionGenerator'),
            $serviceLocator->get('PermitsBilateralInternalBilateralRequiredGenerator'),
            $serviceLocator->get('PermitsBilateralInternalOtherAnswersUpdater'),
            $serviceLocator->get('QaBilateralNoOfPermitsUpdater'),
            $serviceLocator->get('QaApplicationAnswersClearer')
        );
    }
}

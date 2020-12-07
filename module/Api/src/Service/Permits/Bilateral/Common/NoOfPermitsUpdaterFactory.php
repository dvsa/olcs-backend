<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new NoOfPermitsUpdater(
            $repoServiceManager->get('IrhpPermitApplication'),
            $repoServiceManager->get('FeeType'),
            $serviceLocator->get('CqrsCommandCreator'),
            $serviceLocator->get('CommandHandlerManager'),
            $serviceLocator->get('PermitsBilateralApplicationFeesClearer'),
            $serviceLocator->get('CommonCurrentDateTimeFactory')
        );
    }
}

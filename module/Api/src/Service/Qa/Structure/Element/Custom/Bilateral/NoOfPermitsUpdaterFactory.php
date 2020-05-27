<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

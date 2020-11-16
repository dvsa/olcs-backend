<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IrhpPermitApplicationCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpPermitApplicationCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new IrhpPermitApplicationCreator(
            $repoServiceManager->get('IrhpPermitStock'),
            $repoServiceManager->get('IrhpPermitApplication'),
            $serviceLocator->get('PermitsBilateralInternalIrhpPermitApplicationFactory')
        );
    }
}

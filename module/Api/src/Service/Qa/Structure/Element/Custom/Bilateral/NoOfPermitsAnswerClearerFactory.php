<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsAnswerClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsAnswerClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsAnswerClearer(
            $serviceLocator->get('PermitsBilateralApplicationFeesClearer'),
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitApplication')
        );
    }
}

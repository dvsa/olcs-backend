<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitApplication')
        );
    }
}

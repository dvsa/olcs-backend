<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FeeCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeeCreatorFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FeeCreator(
            $serviceLocator->get('RepositoryServiceManager')->get('FeeType'),
            $serviceLocator->get('QaCommandCreator'),
            $serviceLocator->get('CommandHandlerManager'),
            $serviceLocator->get('QaEcmtRemovalNoOfPermitsCurrentDateTimeFactory')
        );
    }
}

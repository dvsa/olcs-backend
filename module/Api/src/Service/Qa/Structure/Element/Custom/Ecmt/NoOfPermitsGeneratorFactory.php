<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsGenerator(
            $serviceLocator->get('RepositoryServiceManager')->get('FeeType'),
            $serviceLocator->get('QaEcmtNoOfPermitsElementFactory'),
            $serviceLocator->get('QaEcmtEmissionsCategoryConditionalAdder'),
            $serviceLocator->get('PermitsAvailabilityStockAvailabilityCounter'),
            $serviceLocator->get('PermitsAvailabilityStockLicenceMaxPermittedCounter')
        );
    }
}

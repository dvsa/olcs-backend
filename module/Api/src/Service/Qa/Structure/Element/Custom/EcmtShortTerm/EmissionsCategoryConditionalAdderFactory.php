<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmissionsCategoryConditionalAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsCategoryConditionalAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new EmissionsCategoryConditionalAdder(
            $serviceLocator->get('QaEcmtShortTermEmissionsCategoryFactory'),
            $repoServiceManager->get('IrhpPermitRange'),
            $repoServiceManager->get('IrhpPermit')
        );
    }
}

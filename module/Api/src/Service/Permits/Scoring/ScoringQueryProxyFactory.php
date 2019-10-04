<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ScoringQueryProxyFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ScoringQueryProxy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ScoringQueryProxy(
            $serviceLocator->get('RepositoryServiceManager')
        );
    }
}

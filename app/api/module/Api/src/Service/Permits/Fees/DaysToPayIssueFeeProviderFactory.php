<?php

namespace Dvsa\Olcs\Api\Service\Permits\Fees;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DaysToPayIssueFeeProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DaysToPayIssueFeeProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DaysToPayIssueFeeProvider(
            $serviceLocator->get('RepositoryServiceManager')->get('SystemParameter')
        );
    }
}

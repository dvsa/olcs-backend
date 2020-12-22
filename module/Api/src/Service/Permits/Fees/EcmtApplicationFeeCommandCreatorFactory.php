<?php

namespace Dvsa\Olcs\Api\Service\Permits\Fees;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EcmtApplicationFeeCommandCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EcmtApplicationFeeCommandCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new EcmtApplicationFeeCommandCreator(
            $serviceLocator->get('RepositoryServiceManager')->get('FeeType'),
            $serviceLocator->get('CqrsCommandCreator'),
            $serviceLocator->get('CommonCurrentDateTimeFactory')
        );
    }
}

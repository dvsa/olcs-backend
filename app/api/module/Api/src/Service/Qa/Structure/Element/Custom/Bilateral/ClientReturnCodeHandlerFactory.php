<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ClientReturnCodeHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ClientReturnCodeHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ClientReturnCodeHandler(
            $serviceLocator->get('PermitsBilateralApplicationCountryRemover')
        );
    }
}

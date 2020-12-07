<?php

/**
 * Payload Validation Listener Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Mvc;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Payload Validation Listener Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PayloadValidationListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PayloadValidationListener($serviceLocator->get('TransferAnnotationBuilder'));
    }
}

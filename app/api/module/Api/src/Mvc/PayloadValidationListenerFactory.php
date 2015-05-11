<?php

/**
 * Payload Validation Listener Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Mvc;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

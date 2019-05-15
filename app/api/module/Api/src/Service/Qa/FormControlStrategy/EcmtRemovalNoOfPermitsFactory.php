<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EcmtRemovalNoOfPermitsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EcmtRemovalNoOfPermits
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new EcmtRemovalNoOfPermits(
            $serviceLocator->get('QaTextFormControlStrategy')
        );
    }
}

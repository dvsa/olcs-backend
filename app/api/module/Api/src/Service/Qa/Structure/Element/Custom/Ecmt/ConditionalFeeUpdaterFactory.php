<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ConditionalFeeUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ConditionalFeeUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ConditionalFeeUpdater(
            $serviceLocator->get('QaEcmtFeeUpdater')
        );
    }
}

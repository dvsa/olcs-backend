<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('QaEcmtShortTermFeeUpdater')
        );
    }
}

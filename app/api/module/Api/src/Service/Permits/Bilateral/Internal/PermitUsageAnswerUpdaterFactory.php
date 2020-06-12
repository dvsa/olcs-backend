<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermitUsageAnswerUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageAnswerUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PermitUsageAnswerUpdater(
            $serviceLocator->get('QaContextFactory'),
            $serviceLocator->get('QaGenericAnswerWriter')
        );
    }
}

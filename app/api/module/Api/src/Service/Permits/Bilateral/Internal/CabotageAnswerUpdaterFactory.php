<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CabotageAnswerUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CabotageAnswerUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CabotageAnswerUpdater(
            $serviceLocator->get('QaContextFactory'),
            $serviceLocator->get('QaGenericAnswerWriter')
        );
    }
}

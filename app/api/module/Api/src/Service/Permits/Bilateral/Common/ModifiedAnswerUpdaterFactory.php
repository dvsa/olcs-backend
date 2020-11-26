<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ModifiedAnswerUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ModifiedAnswerUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ModifiedAnswerUpdater(
            $serviceLocator->get('QaGenericAnswerWriter'),
            $serviceLocator->get('QaApplicationAnswersClearer')
        );
    }
}

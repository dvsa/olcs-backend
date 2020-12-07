<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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

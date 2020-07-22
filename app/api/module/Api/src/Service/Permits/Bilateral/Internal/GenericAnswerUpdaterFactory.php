<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GenericAnswerUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GenericAnswerUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new GenericAnswerUpdater(
            $serviceLocator->get('QaContextFactory'),
            $serviceLocator->get('QaGenericAnswerWriter')
        );
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IntJourneysAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IntJourneysAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new IntJourneysAnswerSaver(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpApplication'),
            $serviceLocator->get('QaGenericAnswerFetcher')
        );
    }
}

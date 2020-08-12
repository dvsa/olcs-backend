<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SectorsAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SectorsAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SectorsAnswerSaver(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpApplication'),
            $serviceLocator->get('QaGenericAnswerFetcher')
        );
    }
}

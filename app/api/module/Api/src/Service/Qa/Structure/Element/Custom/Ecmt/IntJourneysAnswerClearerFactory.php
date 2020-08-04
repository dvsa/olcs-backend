<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IntJourneysAnswerClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IntJourneysAnswerClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new IntJourneysAnswerClearer(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpApplication')
        );
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SectorsAnswerClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SectorsAnswerClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SectorsAnswerClearer(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpApplication')
        );
    }
}

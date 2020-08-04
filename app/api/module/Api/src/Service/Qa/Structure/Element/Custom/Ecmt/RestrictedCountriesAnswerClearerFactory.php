<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RestrictedCountriesAnswerClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RestrictedCountriesAnswerClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new RestrictedCountriesAnswerClearer(
            $serviceLocator->get('QaGenericAnswerClearer'),
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpApplication'),
            $serviceLocator->get('QaCommonArrayCollectionFactory')
        );
    }
}

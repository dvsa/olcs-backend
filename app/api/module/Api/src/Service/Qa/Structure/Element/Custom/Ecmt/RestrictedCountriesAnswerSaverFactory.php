<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RestrictedCountriesAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RestrictedCountriesAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new RestrictedCountriesAnswerSaver(
            $repoServiceManager->get('IrhpApplication'),
            $repoServiceManager->get('Country'),
            $serviceLocator->get('QaCommonArrayCollectionFactory'),
            $serviceLocator->get('QaNamedAnswerFetcher'),
            $serviceLocator->get('QaGenericAnswerWriter'),
            $serviceLocator->get('PermitsCommonStockBasedRestrictedCountryIdsProvider')
        );
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CountryDeletingAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CountryDeletingAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CountryDeletingAnswerSaver(
            $serviceLocator->get('QaGenericAnswerFetcher'),
            $serviceLocator->get('QaGenericAnswerWriter'),
            $serviceLocator->get('QaBilateralClientReturnCodeHandler')
        );
    }
}

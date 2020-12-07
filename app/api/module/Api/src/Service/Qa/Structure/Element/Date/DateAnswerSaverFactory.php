<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DateAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DateAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DateAnswerSaver(
            $serviceLocator->get('QaGenericAnswerWriter'),
            $serviceLocator->get('QaGenericAnswerFetcher'),
            $serviceLocator->get('QaCommonDateTimeFactory')
        );
    }
}

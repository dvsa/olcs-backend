<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RadioAnswerSummaryProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RadioAnswerSummaryProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new RadioAnswerSummaryProvider(
            $serviceLocator->get('QaOptionListGenerator')
        );
    }
}

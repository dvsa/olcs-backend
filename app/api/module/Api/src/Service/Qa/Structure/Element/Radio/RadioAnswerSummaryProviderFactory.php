<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('QaOptionsGenerator')
        );
    }
}

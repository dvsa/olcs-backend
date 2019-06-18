<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormControlStrategyProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FormControlStrategyProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mappings = [
            Question::FORM_CONTROL_TYPE_CHECKBOX => 'QaCheckboxFormControlStrategy',
            Question::FORM_CONTROL_TYPE_TEXT => 'QaTextFormControlStrategy',
            Question::FORM_CONTROL_ECMT_REMOVAL_NO_OF_PERMITS => 'QaEcmtRemovalNoOfPermitsFormControlStrategy',
        ];

        $provider = new FormControlStrategyProvider($mappings);
        foreach ($mappings as $type => $serviceName) {
            $provider->registerStrategy(
                $type,
                $serviceLocator->get($serviceName)
            );
        }

        return $provider;
    }
}
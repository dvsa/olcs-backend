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
            Question::FORM_CONTROL_TYPE_CHECKBOX => $serviceLocator->get('QaCheckboxFormControlStrategy'),
            Question::FORM_CONTROL_TYPE_RADIO => $serviceLocator->get('QaRadioFormControlStrategy'),
            Question::FORM_CONTROL_TYPE_TEXT => $serviceLocator->get('QaTextFormControlStrategy'),
            Question::FORM_CONTROL_ECMT_REMOVAL_NO_OF_PERMITS => $serviceLocator->get(
                'QaEcmtRemovalNoOfPermitsFormControlStrategy'
            )
        ];

        return new FormControlStrategyProvider($mappings);
    }
}

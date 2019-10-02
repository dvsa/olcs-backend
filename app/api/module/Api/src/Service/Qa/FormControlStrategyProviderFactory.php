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
            Question::FORM_CONTROL_TYPE_RADIO => 'QaRadioFormControlStrategy',
            Question::FORM_CONTROL_ECMT_REMOVAL_NO_OF_PERMITS => 'QaEcmtRemovalNoOfPermitsFormControlStrategy',
            Question::FORM_CONTROL_ECMT_SHORT_TERM_NO_OF_PERMITS => 'QaEcmtShortTermNoOfPermitsFormControlStrategy',
            Question::FORM_CONTROL_ECMT_SHORT_TERM_PERMIT_USAGE => 'QaEcmtShortTermPermitUsageFormControlStrategy',
            Question::FORM_CONTROL_ECMT_SHORT_TERM_INTERNATIONAL_JOURNEYS => 'QaEcmtShortTermIntJourneysFormControlStrategy',
            Question::FORM_CONTROL_ECMT_SHORT_TERM_RESTRICTED_COUNTRIES =>
                'QaEcmtShortTermRestrictedCountriesFormControlStrategy',
            Question::FORM_CONTROL_ECMT_SHORT_TERM_ANNUAL_TRIPS_ABROAD =>
                'QaEcmtShortTermAnnualTripsAbroadFormControlStrategy',
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

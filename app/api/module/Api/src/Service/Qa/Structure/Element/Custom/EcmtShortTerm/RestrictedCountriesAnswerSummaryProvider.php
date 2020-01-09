<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;

class RestrictedCountriesAnswerSummaryProvider implements AnswerSummaryProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return 'ecmt-short-term-restricted-countries';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateVariables(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        $isSnapshot
    ) {
        $hasRestrictedCountries = $irhpApplicationEntity->getAnswer($applicationStepEntity);

        $countryNames = [];
        if ($hasRestrictedCountries) {
            foreach ($irhpApplicationEntity->getCountrys() as $country) {
                $countryNames[] = $country->getCountryDesc();
            }
        }

        return [
            'hasRestrictedCountries' => $hasRestrictedCountries,
            'countryNames' => $countryNames
        ];
    }
}

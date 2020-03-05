<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class RestrictedCountriesAnswerSummaryProvider implements AnswerSummaryProviderInterface
{
    use IrhpApplicationOnlyTrait;

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
    public function getTemplateVariables(QaContext $qaContext, $isSnapshot)
    {
        $irhpApplicationEntity = $qaContext->getQaEntity();
        $hasRestrictedCountries = $qaContext->getAnswerValue();

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

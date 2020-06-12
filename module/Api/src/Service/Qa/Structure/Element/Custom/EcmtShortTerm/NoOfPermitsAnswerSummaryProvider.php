<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AlwaysIncludeSlugTrait;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class NoOfPermitsAnswerSummaryProvider implements AnswerSummaryProviderInterface
{
    use AlwaysIncludeSlugTrait, IrhpApplicationOnlyTrait;

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return 'ecmt-short-term-no-of-permits';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateVariables(QaContext $qaContext, $isSnapshot)
    {
        $irhpPermitApplication = $qaContext->getQaEntity()->getFirstIrhpPermitApplication();

        $emissionsCategories = [
            [
                'key' => 'qanda.common.no-of-permits.emissions-category.euro5',
                'count' => $irhpPermitApplication->getRequiredEuro5()
            ],
            [
                'key' => 'qanda.common.no-of-permits.emissions-category.euro6',
                'count' => $irhpPermitApplication->getRequiredEuro6()
            ],
        ];

        $irhpPermitStock = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock();

        return [
            'validityYear' => $irhpPermitStock->getValidityYear(),
            'periodNameKey' => $irhpPermitStock->getPeriodNameKey(),
            'emissionsCategories' => $emissionsCategories
        ];
    }
}

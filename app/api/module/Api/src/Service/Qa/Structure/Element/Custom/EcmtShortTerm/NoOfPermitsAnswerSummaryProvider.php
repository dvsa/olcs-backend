<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;

class NoOfPermitsAnswerSummaryProvider implements AnswerSummaryProviderInterface
{
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
    public function getTemplateVariables(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        $isSnapshot
    ) {
        $irhpPermitApplication = $irhpApplicationEntity->getFirstIrhpPermitApplication();

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
            'periodNameKey' => $irhpPermitStock->getPeriodNameKey(),
            'emissionsCategories' => $emissionsCategories
        ];
    }
}

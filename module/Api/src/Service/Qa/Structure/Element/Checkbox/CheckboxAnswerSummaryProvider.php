<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Checkbox;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;

class CheckboxAnswerSummaryProvider implements AnswerSummaryProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return 'generic';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateVariables(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        $isSnapshot
    ) {
        $answerValue = 'No';
        if ($irhpApplicationEntity->getAnswer($applicationStepEntity)) {
            $answerValue = 'Yes';
        }

        return [
            'answer' => $answerValue
        ];
    }
}

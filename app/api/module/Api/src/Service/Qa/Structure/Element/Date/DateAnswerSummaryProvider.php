<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date;

use DateTime;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;

class DateAnswerSummaryProvider implements AnswerSummaryProviderInterface
{
    const DATE_FORMAT = 'd/m/Y';

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
        $answerValue = $irhpApplicationEntity->getAnswer($applicationStepEntity);
        $dateTime = new DateTime($answerValue);

        return [
            'answer' => $dateTime->format(self::DATE_FORMAT)
        ];
    }
}

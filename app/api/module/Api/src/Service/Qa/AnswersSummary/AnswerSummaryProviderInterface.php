<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;

interface AnswerSummaryProviderInterface
{
    /**
     * Return the template name to be used in building the answer summary
     *
     * @return string
     */
    public function getTemplateName();

    /**
     * Return the template variables to be used in building the answer summary
     *
     * @param ApplicationStepEntity $applicationStepEntity
     * @param IrhpApplicationEntity $irhpApplicationEntity
     * @param bool $isSnapshot
     *
     * @return array
     */
    public function getTemplateVariables(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        $isSnapshot
    );
}

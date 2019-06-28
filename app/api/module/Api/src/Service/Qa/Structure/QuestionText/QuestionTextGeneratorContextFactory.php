<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;

class QuestionTextGeneratorContextFactory
{
    /**
     * Create and return an QuestionTextGeneratorContext instance
     *
     * @param ApplicationStepEntity $applicationStepEntity
     * @param IrhpApplicationEntity $irhpApplicationEntity
     *
     * @return QuestionTextGeneratorContext
     */
    public function create(ApplicationStepEntity $applicationStepEntity, IrhpApplicationEntity $irhpApplicationEntity)
    {
        return new QuestionTextGeneratorContext($applicationStepEntity, $irhpApplicationEntity);
    }
}

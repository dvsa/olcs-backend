<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;

class QuestionTextGeneratorContext
{
    /** @var ApplicationStepEntity */
    private $applicationStepEntity;

    /** @var IrhpApplicationEntity */
    private $irhpApplicationEntity;

    /**
     * Create instance
     *
     * @param ApplicationStepEntity $applicationStepEntity
     * @param IrhpApplicationEntity $irhpApplicationEntity
     *
     * @return QuestionTextGeneratorContext
     */
    public function __construct(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity
    ) {
        $this->applicationStepEntity = $applicationStepEntity;
        $this->irhpApplicationEntity = $irhpApplicationEntity;
    }

    /**
     * Get the embedded ApplicationStep instance
     *
     * @return ApplicationStepEntity
     */
    public function getApplicationStepEntity()
    {
        return $this->applicationStepEntity;
    }

    /**
     * Get the embedded IrhpApplication instance
     *
     * @return IrhpApplicationEntity
     */
    public function getIrhpApplicationEntity()
    {
        return $this->irhpApplicationEntity;
    }
}

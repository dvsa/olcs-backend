<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;

class QaContext
{
    /** @var ApplicationStepEntity */
    private $applicationStepEntity;

    /** @var QaEntityInterface */
    private $qaEntity;

    /**
     * Create instance
     *
     * @param ApplicationStepEntity $applicationStepEntity
     * @param QaEntityInterface $qaEntity
     *
     * @return QaContext
     */
    public function __construct(ApplicationStepEntity $applicationStepEntity, QaEntityInterface $qaEntity)
    {
        $this->applicationStepEntity = $applicationStepEntity;
        $this->qaEntity = $qaEntity;
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

    /*
     * Get the embedded QaEntityInterface instance
     *
     * @return QaEntityInterface
     */
    public function getQaEntity()
    {
        return $this->qaEntity;
    }

    /*
     * Get the answer value associated with this context
     *
     * @return mixed|null
     */
    public function getAnswerValue()
    {
        return $this->qaEntity->getAnswer($this->applicationStepEntity);
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorList;

class ElementGeneratorContext
{
    /** @var ValidatorList */
    private $validatorList;

    /** @var ApplicationStepEntity */
    private $applicationStepEntity;

    /** @var IrhpApplicationEntity */
    private $irhpApplicationEntity;

    /**
     * Create instance
     *
     * @param ValidatorList $validatorList
     * @param ApplicationStepEntity $applicationStepEntity
     * @param IrhpApplicationEntity $irhpApplicationEntity
     *
     * @return ElementGeneratorContext
     */
    public function __construct(
        ValidatorList $validatorList,
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity
    ) {
        $this->validatorList = $validatorList;
        $this->applicationStepEntity = $applicationStepEntity;
        $this->irhpApplicationEntity = $irhpApplicationEntity;
    }

    /**
     * Get the embedded ValidatorList instance
     *
     * @return ValidatorList
     */
    public function getValidatorList()
    {
        return $this->validatorList;
    }

    /**
     * Get the embedded ApplicationStepEntity instance
     *
     * @return ApplicationStepEntity
     */
    public function getApplicationStepEntity()
    {
        return $this->applicationStepEntity;
    }

    /**
     * Get the embedded IrhpApplicationEntity instance
     *
     * @return ApplicationStepEntity
     */
    public function getIrhpApplicationEntity()
    {
        return $this->irhpApplicationEntity;
    }
}

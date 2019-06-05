<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Answer as AnswerEntity;

class ApplicationStepFactory
{
    /**
     * Create and return an ApplicationStep instance
     *
     * @param string $type
     * @param string $fieldsetName
     * @param ElementInterface $element
     * @param ValidatorList $validatorList
     *
     * @return ApplicationStep
     */
    public function create($type, $fieldsetName, ElementInterface $element, ValidatorList $validatorList)
    {
        return new ApplicationStep($type, $fieldsetName, $element, $validatorList);
    }
}

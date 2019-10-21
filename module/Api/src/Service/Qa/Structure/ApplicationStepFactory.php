<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Answer as AnswerEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class ApplicationStepFactory
{
    /**
     * Create and return an ApplicationStep instance
     *
     * @param string $type
     * @param string $fieldsetName
     * @param string $shortName
     * @param ElementInterface $element
     * @param ValidatorList $validatorList
     *
     * @return ApplicationStep
     */
    public function create($type, $fieldsetName, $shortName, ElementInterface $element, ValidatorList $validatorList)
    {
        return new ApplicationStep($type, $fieldsetName, $shortName, $element, $validatorList);
    }
}

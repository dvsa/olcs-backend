<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Answer as AnswerEntity;

class ApplicationStep
{
    /** @var string */
    private $type;

    /** @var string */
    private $fieldsetName;

    /** @var ElementInterface */
    private $element;

    /** @var ValidatorList */
    private $validatorList;

    /**
     * Create instance
     *
     * @param string $type
     * @param string $fieldsetName
     * @param ElementInterface $element
     * @param ValidatorList $validatorList
     *
     * @return ApplicationStep
     */
    public function __construct($type, $fieldsetName, ElementInterface $element, ValidatorList $validatorList)
    {
        $this->type = $type;
        $this->fieldsetName = $fieldsetName;
        $this->element = $element;
        $this->validatorList = $validatorList;
    }

    /**
     * Get the representation of this class to be returned by the API endpoint
     *
     * @return array
     */
    public function getRepresentation()
    {
        return [
            'type' => $this->type,
            'fieldsetName' => $this->fieldsetName,
            'element' => $this->element->getRepresentation(),
            'validators' => $this->validatorList->getRepresentation(),
        ];
    }
}

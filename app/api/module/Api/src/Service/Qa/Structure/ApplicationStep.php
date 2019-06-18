<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

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

    /**
     * Get the embedded ValidatorList instance
     *
     * @return ValidatorList
     */
    public function getValidatorList()
    {
        return $this->validatorList;
    }
}
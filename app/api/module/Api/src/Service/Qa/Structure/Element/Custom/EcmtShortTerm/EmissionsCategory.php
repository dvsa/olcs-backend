<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

class EmissionsCategory
{
    /** @var string */
    private $name;

    /** @var string */
    private $labelTranslationKey;

    /** @var int|null */
    private $value;

    /** @var int */
    private $maxValue;

    /**
     * Create instance
     *
     * @param string $name
     * @param string $labelTranslationKey
     * @param int|null $value
     * @param int $maxValue
     *
     * @return EmissionsCategory
     */
    public function __construct($name, $labelTranslationKey, $value, $maxValue)
    {
        $this->name = $name;
        $this->labelTranslationKey = $labelTranslationKey;
        $this->value = $value;
        $this->maxValue = $maxValue;
    }

    /**
     * Get the representation of this class to be returned by the API endpoint
     *
     * @return array
     */
    public function getRepresentation()
    {
        return [
            'name' => $this->name,
            'labelTranslationKey' => $this->labelTranslationKey,
            'value' => $this->value,
            'maxValue' => $this->maxValue,
        ];
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

class Option
{
    /** @var string */
    private $value;

    /** @var string */
    private $label;

    /** @var string|null */
    private $hint;

    /**
     * Create instance
     *
     * @param string $value
     * @param string $label
     * @param string|null $hint
     *
     * @return Option
     */
    public function __construct($value, $label, $hint = null)
    {
        $this->value = $value;
        $this->label = $label;
        $this->hint = $hint;
    }

    /**
     * Get the representation of this element to be returned by the API endpoint
     *
     * @return array
     */
    public function getRepresentation()
    {
        $representation = [
            'value' => $this->value,
            'label' => $this->label,
        ];

        if ($this->hint) {
            $representation['hint'] = $this->hint;
        }

        return $representation;
    }
}

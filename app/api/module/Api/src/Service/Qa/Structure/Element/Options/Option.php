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
     * Get the representation of this option to be returned by the API endpoint
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

    /**
     * Get the value of this option
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the label of this option
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label of this option
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Set the hint of this option
     *
     * @param string $hint
     */
    public function setHint($hint)
    {
        $this->hint = $hint;
    }
}

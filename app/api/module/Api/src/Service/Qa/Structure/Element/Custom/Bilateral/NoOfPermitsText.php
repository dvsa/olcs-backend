<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

class NoOfPermitsText
{
    /** @var string */
    private $name;

    /** @var string */
    private $label;

    /** @var string */
    private $hint;

    /** @var string|null */
    private $value;

    /**
     * Create instance
     *
     * @param string $name
     * @param string $label
     * @param string $hint
     * @param string|null $value
     *
     * @return NoOfPermitsText
     */
    public function __construct($name, $label, $hint, $value)
    {
        $this->name = $name;
        $this->label = $label;
        $this->hint = $hint;
        $this->value = $value;
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
            'label' => $this->label,
            'hint' => $this->hint,
            'value' => $this->value
        ];
    }
}

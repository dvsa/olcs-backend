<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

class TranslateableTextParameter
{
    /** @var string */
    private $value;

    /** @var string|null */
    private $formatter;

    /**
     * Create instance
     *
     * @param string $value
     * @param string|null $formatter
     *
     * @return TranslateableText
     */
    public function __construct($value, $formatter)
    {
        $this->value = $value;
        $this->formatter = $formatter;
    }

    /**
     * Get the representation of this class to be returned by the API endpoint
     *
     * @return array
     */
    public function getRepresentation()
    {
        $representation = ['value' => $this->value];
        if (!is_null($this->formatter)) {
            $representation['formatter'] = $this->formatter;
        }

        return $representation;
    }

    /**
     * Set the value of this parameter
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}

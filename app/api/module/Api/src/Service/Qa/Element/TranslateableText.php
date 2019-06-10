<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use RuntimeException;

class TranslateableText
{
    /** @var string */
    private $key;

    /** @var array */
    private $parameters;

    /**
     * Create instance
     *
     * @param string $key
     *
     * @return TranslateableText
     */
    public function __construct($key)
    {
        $this->key = $key;
        $this->parameters = [];
    }

    /**
     * Get the representation of this class to be returned by the API endpoint
     *
     * @return array
     */
    public function getRepresentation()
    {
        return [
            'key' => $this->key,
            'parameters' => $this->parameters
        ];
    }

    /**
     * Add a parameter to be used when substituting placeholders in the string derived from the translation key
     *
     * @param string $value
     */
    public function addParameter($value)
    {
        $this->parameters[] = $value;
    }

    /**
     * Set the value of a parameter to be used when substitusing placeholders in the string derived from the
     * translation key
     *
     * @param int $index
     * @param string $value
     */
    public function setParameter($index, $value)
    {
        if (!isset($this->parameters[$index])) {
            throw new RuntimeException('No parameter exists at index ' . $index);
        }

        $this->parameters[$index] = $value;
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

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
        $representation = ['key' => $this->key];

        $parametersRepresentation = [];
        foreach ($this->parameters as $parameter) {
            $parametersRepresentation[] = $parameter->getRepresentation();
        }

        $representation['parameters'] = $parametersRepresentation;

        return $representation;
    }

    /**
     * Add a parameter to be used when substituting placeholders in the string derived from the translation key
     *
     * @param string $value
     */
    public function addParameter(TranslateableTextParameter $translateableTextParameter)
    {
        $this->parameters[] = $translateableTextParameter;
    }

    /**
     * Gets the parameter at the specified index
     *
     * @param int $index
     */
    public function getParameter($index)
    {
        if (!isset($this->parameters[$index])) {
            throw new RuntimeException('No parameter exists at index ' . $index);
        }

        return $this->parameters[$index];
    }
}

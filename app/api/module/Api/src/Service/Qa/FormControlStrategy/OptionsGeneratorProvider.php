<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

use RuntimeException;

class OptionsGeneratorProvider
{
    /** @var array */
    private $mappings;

    /**
     * Create service instance
     *
     * @param array $mappings
     *
     * @return OptionsGeneratorProvider
     */
    public function __construct(array $mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * Returns an implementation of OptionsGeneratorInterface corresponding to the provided form control type name
     *
     * @param string $optionsGeneratorType
     *
     * @return OptionsGeneratorInterface
     *
     * @throws RuntimeException if no options generator exists for the specified name
     */
    public function get($optionsGeneratorType)
    {
        if (!isset($this->mappings[$optionsGeneratorType])) {
            throw new RuntimeException('No OptionsGenerator found for option source type ' . $optionsGeneratorType);
        }

        return $this->mappings[$optionsGeneratorType];
    }
}

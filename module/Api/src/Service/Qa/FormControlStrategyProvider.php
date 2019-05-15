<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use RuntimeException;

class FormControlStrategyProvider
{
    /** @var array */
    private $mappings;

    /**
     * Create service instance
     *
     * @param array $mappings
     *
     * @return FormControlStrategyProvider
     */
    public function __construct(array $mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * Returns an implementation of FormControlStrategyInterface corresponding to the provided form control type name
     *
     * @param string $formControlType
     *
     * @return FormControlStrategyInterface
     *
     * @throws RuntimeException if no strategy exists for the specified name
     */
    public function get($formControlType)
    {
        if (!isset($this->mappings[$formControlType])) {
            throw new RuntimeException('No FormControlStrategy found for form control type ' . $formControlType);
        }

        return $this->mappings[$formControlType];
    }
}

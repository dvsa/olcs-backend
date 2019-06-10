<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use RuntimeException;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;

class FormControlStrategyProvider
{
    /** @var array */
    private $mappings;

    /**
     * Create service instance
     *
     * @return FormControlStrategyProvider
     */
    public function __construct()
    {
        $this->mappings = [];
    }

    /**
     * Returns an implementation of FormControlStrategyInterface corresponding to the provided application step entity
     *
     * @param ApplicationStep $applicationStep
     *
     * @return FormControlStrategyInterface
     *
     * @throws RuntimeException if no strategy exists for the specified name
     */
    public function get(ApplicationStep $applicationStep)
    {
        $formControlType = $applicationStep->getQuestion()->getFormControlType()->getId();

        if (!isset($this->mappings[$formControlType])) {
            throw new RuntimeException('No FormControlStrategy found for form control type ' . $formControlType);
        }

        return $this->mappings[$formControlType];
    }

    /**
     * Registers an implementation of FormControlStrategyInterface corresponding to the provided form control type name
     *
     * @param string $formControlType
     * @param FormControlStrategyInterface $strategy
     */
    public function registerStrategy($formControlType, FormControlStrategyInterface $strategy)
    {
        $this->mappings[$formControlType] = $strategy;
    }
}

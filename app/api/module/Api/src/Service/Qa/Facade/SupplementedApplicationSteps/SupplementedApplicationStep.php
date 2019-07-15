<?php

namespace Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;

class SupplementedApplicationStep
{
    /** @var ApplicationStep */
    private $applicationStep;

    /** @var FormControlStrategyInterface */
    private $formControlStrategy;

    /**
     * Create instance
     *
     * @param ApplicationStep $applicationStep
     * @param FormControlStrategyInterface $formControlStrategy
     *
     * @return SupplementedApplicationStep
     */
    public function __construct(ApplicationStep $applicationStep, FormControlStrategyInterface $formControlStrategy)
    {
        $this->applicationStep = $applicationStep;
        $this->formControlStrategy = $formControlStrategy;
    }

    /**
     * Get the embedded ApplicationStep instance
     *
     * @return ApplicationStep
     */
    public function getApplicationStep()
    {
        return $this->applicationStep;
    }

    /**
     * Get the embedded FormControlStrategyInterface instance
     *
     * @return FormControlStrategyInterface
     */
    public function getFormControlStrategy()
    {
        return $this->formControlStrategy;
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps;

use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;

class SupplementedApplicationStepsProvider
{
    /** @var FormControlStrategyProvider */
    private $formControlStrategyProvider;

    /** @var SupplementedApplicationStepFactory */
    private $supplementedApplicationStepFactory;

    /**
     * Create service instance
     *
     * @param FormControlStrategyProvider $formControlStrategyProvider
     * @param SupplementedApplicationStepFactory $supplementedApplicationStepFactory
     *
     * @return SupplementedApplicationStepsProvider
     */
    public function __construct(
        FormControlStrategyProvider $formControlStrategyProvider,
        SupplementedApplicationStepFactory $supplementedApplicationStepFactory
    ) {
        $this->formControlStrategyProvider = $formControlStrategyProvider;
        $this->supplementedApplicationStepFactory = $supplementedApplicationStepFactory;
    }

    /**
     * Get a list of application steps and associated form control strategies for the specified qa entity
     *
     * @param QaEntityInterface $qaEntity
     *
     * @return array
     */
    public function get(QaEntityInterface $qaEntity)
    {
        $applicationPath = $qaEntity->getActiveApplicationPath();

        $supplementedApplicationSteps = [];

        foreach ($applicationPath->getApplicationSteps() as $applicationStep) {
            $supplementedApplicationSteps[] = $this->supplementedApplicationStepFactory->create(
                $applicationStep,
                $this->formControlStrategyProvider->get($applicationStep)
            );
        }

        return $supplementedApplicationSteps;
    }
}

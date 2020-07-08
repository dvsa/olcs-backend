<?php

namespace Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps;

use Dvsa\Olcs\Api\Domain\FormControlServiceManager;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;

class SupplementedApplicationStepsProvider
{
    /** @var FormControlServiceManager */
    private $formControlServiceManager;

    /** @var SupplementedApplicationStepFactory */
    private $supplementedApplicationStepFactory;

    /**
     * Create service instance
     *
     * @param FormControlServiceManager $formControlServiceManager
     * @param SupplementedApplicationStepFactory $supplementedApplicationStepFactory
     *
     * @return SupplementedApplicationStepsProvider
     */
    public function __construct(
        FormControlServiceManager $formControlServiceManager,
        SupplementedApplicationStepFactory $supplementedApplicationStepFactory
    ) {
        $this->formControlServiceManager = $formControlServiceManager;
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
                $this->formControlServiceManager->getByApplicationStep($applicationStep)
            );
        }

        return $supplementedApplicationSteps;
    }
}

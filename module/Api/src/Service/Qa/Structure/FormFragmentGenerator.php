<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;

class FormFragmentGenerator
{
    /** @var FormFragmentFactory */
    private $formFragmentFactory;

    /** @var ApplicationStepGenerator */
    private $applicationStepGenerator;

    /**
     * Create service instance
     *
     * @param FormFragmentFactory $formFragmentFactory
     * @param ApplicationStepGenerator $applicationStepGenerator
     *
     * @return FormFragmentGenerator
     */
    public function __construct(
        FormFragmentFactory $formFragmentFactory,
        ApplicationStepGenerator $applicationStepGenerator
    ) {
        $this->formFragmentFactory = $formFragmentFactory;
        $this->applicationStepGenerator = $applicationStepGenerator;
    }

    /**
     * Build and return a FormFragment instance using the appropriate data sources
     *
     * @param array $applicationStepEntities
     * @param IrhpApplicationEntity $irhpApplicationEntity
     *
     * @return FormFragment
     */
    public function generate(array $applicationStepEntities, IrhpApplicationEntity $irhpApplicationEntity)
    {
        $formFragment = $this->formFragmentFactory->create();

        foreach ($applicationStepEntities as $applicationStepEntity) {
            $formFragment->addApplicationStep(
                $this->applicationStepGenerator->generate($applicationStepEntity, $irhpApplicationEntity)
            );
        }

        return $formFragment;
    }
}

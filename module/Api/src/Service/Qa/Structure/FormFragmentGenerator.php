<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;

class FormFragmentGenerator
{
    /** @var FormFragmentFactory */
    private $formFragmentFactory;

    /** @var ApplicationStepGenerator */
    private $applicationStepGenerator;

    /** @var QaContextFactory */
    private $qaContextFactory;

    /**
     * Create service instance
     *
     * @param FormFragmentFactory $formFragmentFactory
     * @param ApplicationStepGenerator $applicationStepGenerator
     * @param QaContextFactory $qaContextFactory
     *
     * @return FormFragmentGenerator
     */
    public function __construct(
        FormFragmentFactory $formFragmentFactory,
        ApplicationStepGenerator $applicationStepGenerator,
        QaContextFactory $qaContextFactory
    ) {
        $this->formFragmentFactory = $formFragmentFactory;
        $this->applicationStepGenerator = $applicationStepGenerator;
        $this->qaContextFactory = $qaContextFactory;
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
            $qaContext = $this->qaContextFactory->create($applicationStepEntity, $irhpApplicationEntity);

            $formFragment->addApplicationStep(
                $this->applicationStepGenerator->generate($qaContext)
            );
        }

        return $formFragment;
    }
}

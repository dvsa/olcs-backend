<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Domain\FormControlServiceManager;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContextFactory;

class ApplicationStepGenerator
{
    /** @var FormControlServiceManager */
    private $formControlServiceManager;

    /** @var ApplicationStepFactory */
    private $applicationStepFactory;

    /** @var ValidatorListGenerator */
    private $validatorListGenerator;

    /** @var ElementGeneratorContextFactory */
    private $elementGeneratorContextFactory;

    /**
     * Create service instance
     *
     * @param FormControlServiceManager $formControlServiceManager
     * @param ApplicationStepFactory $applicationStepFactory
     * @param ValidatorListGenerator $validatorListGenerator
     * @param ElementGeneratorContextFactory $elementGeneratorContextFactory
     *
     * @return ApplicationStepGenerator
     */
    public function __construct(
        FormControlServiceManager $formControlServiceManager,
        ApplicationStepFactory $applicationStepFactory,
        ValidatorListGenerator $validatorListGenerator,
        ElementGeneratorContextFactory $elementGeneratorContextFactory
    ) {
        $this->formControlServiceManager = $formControlServiceManager;
        $this->applicationStepFactory = $applicationStepFactory;
        $this->validatorListGenerator = $validatorListGenerator;
        $this->elementGeneratorContextFactory = $elementGeneratorContextFactory;
    }

    /**
     * Build and return an ApplicationStep instance using the appropriate data sources
     *
     * @param QaContext $qaContext
     *
     * @return ApplicationStep
     */
    public function generate(QaContext $qaContext)
    {
        $applicationStepEntity = $qaContext->getApplicationStepEntity();

        $formControlStrategy = $this->formControlServiceManager->getByApplicationStep($applicationStepEntity);
        $validatorList = $this->validatorListGenerator->generate($applicationStepEntity);

        $elementGeneratorContext = $this->elementGeneratorContextFactory->create($validatorList, $qaContext);
        $applicationStepEntity = $qaContext->getApplicationStepEntity();

        return $this->applicationStepFactory->create(
            $formControlStrategy->getFrontendType(),
            $applicationStepEntity->getFieldsetName(),
            $applicationStepEntity->getQuestion()->getActiveQuestionText()->getQuestionShortKey(),
            $formControlStrategy->getElement($elementGeneratorContext),
            $validatorList
        );
    }
}

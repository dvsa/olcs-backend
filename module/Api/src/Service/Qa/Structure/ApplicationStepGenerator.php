<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContextFactory;

class ApplicationStepGenerator
{
    /** @var FormControlStrategyProvider */
    private $formControlStrategyProvider;

    /** @var ApplicationStepFactory */
    private $applicationStepFactory;

    /** @var ValidatorListGenerator */
    private $validatorListGenerator;

    /** @var ElementGeneratorContextFactory */
    private $elementGeneratorContextFactory;

    /**
     * Create service instance
     *
     * @param FormControlStrategyProvider $formControlStrategyProvider
     * @param ApplicationStepFactory $applicationStepFactory
     * @param ValidatorListGenerator $validatorListGenerator
     * @param ElementGeneratorContextFactory $elementGeneratorContextFactory
     *
     * @return ApplicationStepGenerator
     */
    public function __construct(
        FormControlStrategyProvider $formControlStrategyProvider,
        ApplicationStepFactory $applicationStepFactory,
        ValidatorListGenerator $validatorListGenerator,
        ElementGeneratorContextFactory $elementGeneratorContextFactory
    ) {
        $this->formControlStrategyProvider = $formControlStrategyProvider;
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

        $formControlStrategy = $this->formControlStrategyProvider->get($applicationStepEntity);
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

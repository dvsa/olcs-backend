<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Domain\FormControlServiceManager;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContextGenerator;

class ApplicationStepGenerator
{
    /** @var FormControlServiceManager */
    private $formControlServiceManager;

    /** @var ApplicationStepFactory */
    private $applicationStepFactory;

    /** @var ElementGeneratorContextGenerator */
    private $elementGeneratorContextGenerator;

    /**
     * Create service instance
     *
     * @param FormControlServiceManager $formControlServiceManager
     * @param ApplicationStepFactory $applicationStepFactory
     * @param ElementGeneratorContextGenerator $elementGeneratorContextGenerator
     *
     * @return ApplicationStepGenerator
     */
    public function __construct(
        FormControlServiceManager $formControlServiceManager,
        ApplicationStepFactory $applicationStepFactory,
        ElementGeneratorContextGenerator $elementGeneratorContextGenerator
    ) {
        $this->formControlServiceManager = $formControlServiceManager;
        $this->applicationStepFactory = $applicationStepFactory;
        $this->elementGeneratorContextGenerator = $elementGeneratorContextGenerator;
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
        $elementGeneratorContext = $this->elementGeneratorContextGenerator->generate($qaContext);
        $element = $formControlStrategy->getElement($elementGeneratorContext);

        return $this->applicationStepFactory->create(
            $formControlStrategy->getFrontendType(),
            $applicationStepEntity->getFieldsetName(),
            $applicationStepEntity->getQuestion()->getActiveQuestionText()->getQuestionShortKey(),
            $element,
            $elementGeneratorContext->getValidatorList()
        );
    }
}

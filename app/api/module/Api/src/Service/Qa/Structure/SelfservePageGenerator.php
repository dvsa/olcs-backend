<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContextFactory;

class SelfservePageGenerator
{
    /** @var SelfservePageFactory */
    private $selfservePageFactory;

    /** @var ApplicationStepGenerator */
    private $applicationStepGenerator;

    /** @var FormControlStrategyProvider */
    private $formControlStrategyProvider;

    /** @var QuestionTextGeneratorContextFactory */
    private $questionTextGeneratorContextFactory;

    /**
     * Create service instance
     *
     * @param SelfservePageFactory $selfservePageFactory
     * @param ApplicationStepGenerator $applicationStepGenerator
     * @param FormControlStrategyProvider $formControlStrategyProvider
     * @param QuestionTextGeneratorContextFactory $questionTextGeneratorContextFactory
     *
     * @return SelfservePageGenerator
     */
    public function __construct(
        SelfservePageFactory $selfservePageFactory,
        ApplicationStepGenerator $applicationStepGenerator,
        FormControlStrategyProvider $formControlStrategyProvider,
        QuestionTextGeneratorContextFactory $questionTextGeneratorContextFactory
    ) {
        $this->selfservePageFactory = $selfservePageFactory;
        $this->applicationStepGenerator = $applicationStepGenerator;
        $this->formControlStrategyProvider = $formControlStrategyProvider;
        $this->questionTextGeneratorContextFactory = $questionTextGeneratorContextFactory;
    }

    /**
     * Build and return a Selfserve instance using the appropriate data sources
     *
     * @param ApplicationStepEntity $applicationStepEntity
     * @param IrhpApplicationEntity $irhpApplicationEntity
     *
     * @return SelfservePage
     */
    public function generate(ApplicationStepEntity $applicationStepEntity, IrhpApplicationEntity $irhpApplicationEntity)
    {
        $questionTextGeneratorContext = $this->questionTextGeneratorContextFactory->create(
            $applicationStepEntity,
            $irhpApplicationEntity
        );

        $formControlStrategy = $this->formControlStrategyProvider->get($applicationStepEntity);

        $selfservePage = $this->selfservePageFactory->create(
            $irhpApplicationEntity->getApplicationRef(),
            $this->applicationStepGenerator->generate($applicationStepEntity, $irhpApplicationEntity),
            $formControlStrategy->getQuestionText($questionTextGeneratorContext),
            $applicationStepEntity->getNextStepSlug()
        );

        return $selfservePage;
    }
}

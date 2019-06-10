<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;

class SelfservePageGenerator
{
    /** @var SelfservePageFactory */
    private $selfservePageFactory;

    /** @var QuestionTextGenerator */
    private $questionTextGenerator;

    /** @var ApplicationStepGenerator */
    private $applicationStepGenerator;

    /** @var FormControlStrategyProvider */
    private $formControlStrategyProvider;

    /**
     * Create service instance
     *
     * @param SelfservePageFactory $selfservePageFactory
     * @param QuestionTextGenerator $questionTextGenerator
     * @param ApplicationStepGenerator $applicationStepGenerator
     * @param FormControlStrategyProvider $formControlStrategyProvider
     *
     * @return SelfservePageGenerator
     */
    public function __construct(
        SelfservePageFactory $selfservePageFactory,
        QuestionTextGenerator $questionTextGenerator,
        ApplicationStepGenerator $applicationStepGenerator,
        FormControlStrategyProvider $formControlStrategyProvider
    ) {
        $this->selfservePageFactory = $selfservePageFactory;
        $this->questionTextGenerator = $questionTextGenerator;
        $this->applicationStepGenerator = $applicationStepGenerator;
        $this->formControlStrategyProvider = $formControlStrategyProvider;
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
        $selfservePage = $this->selfservePageFactory->create(
            $irhpApplicationEntity->getApplicationRef(),
            $this->applicationStepGenerator->generate($applicationStepEntity, $irhpApplicationEntity),
            $this->questionTextGenerator->generate(
                $applicationStepEntity->getQuestion()->getActiveQuestionText()
            ),
            $applicationStepEntity->getNextStepSlug()
        );

        $formControlStrategy = $this->formControlStrategyProvider->get($applicationStepEntity);
        $formControlStrategy->postProcessSelfservePage($selfservePage);

        return $selfservePage;
    }
}

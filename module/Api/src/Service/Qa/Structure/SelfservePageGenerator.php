<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Api\Service\Qa\QaContext;

class SelfservePageGenerator
{
    /** @var SelfservePageFactory */
    private $selfservePageFactory;

    /** @var ApplicationStepGenerator */
    private $applicationStepGenerator;

    /** @var FormControlStrategyProvider */
    private $formControlStrategyProvider;

    /**
     * Create service instance
     *
     * @param SelfservePageFactory $selfservePageFactory
     * @param ApplicationStepGenerator $applicationStepGenerator
     * @param FormControlStrategyProvider $formControlStrategyProvider
     *
     * @return SelfservePageGenerator
     */
    public function __construct(
        SelfservePageFactory $selfservePageFactory,
        ApplicationStepGenerator $applicationStepGenerator,
        FormControlStrategyProvider $formControlStrategyProvider
    ) {
        $this->selfservePageFactory = $selfservePageFactory;
        $this->applicationStepGenerator = $applicationStepGenerator;
        $this->formControlStrategyProvider = $formControlStrategyProvider;
    }

    /**
     * Build and return a Selfserve instance using the appropriate data sources
     *
     * @param QaContext $qaContext
     *
     * @return SelfservePage
     */
    public function generate(QaContext $qaContext)
    {
        $applicationStepEntity = $qaContext->getApplicationStepEntity();
        $qaEntity = $qaContext->getQaEntity();

        $formControlStrategy = $this->formControlStrategyProvider->get($applicationStepEntity);

        $selfservePage = $this->selfservePageFactory->create(
            $applicationStepEntity->getQuestion()->getActiveQuestionText()->getQuestionShortKey(),
            $qaEntity->getAdditionalQaViewData(),
            $this->applicationStepGenerator->generate($qaContext),
            $formControlStrategy->getQuestionText($qaContext),
            $applicationStepEntity->getNextStepSlug()
        );

        return $selfservePage;
    }
}

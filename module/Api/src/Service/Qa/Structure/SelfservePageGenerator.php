<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Domain\FormControlServiceManager;
use Dvsa\Olcs\Api\Service\Qa\QaContext;

class SelfservePageGenerator
{
    /** @var SelfservePageFactory */
    private $selfservePageFactory;

    /** @var ApplicationStepGenerator */
    private $applicationStepGenerator;

    /** @var FormControlServiceManager */
    private $formControlServiceManager;

    /**
     * Create service instance
     *
     * @param SelfservePageFactory $selfservePageFactory
     * @param ApplicationStepGenerator $applicationStepGenerator
     * @param FormControlServiceManager $formControlServiceManager
     *
     * @return SelfservePageGenerator
     */
    public function __construct(
        SelfservePageFactory $selfservePageFactory,
        ApplicationStepGenerator $applicationStepGenerator,
        FormControlServiceManager $formControlServiceManager
    ) {
        $this->selfservePageFactory = $selfservePageFactory;
        $this->applicationStepGenerator = $applicationStepGenerator;
        $this->formControlServiceManager = $formControlServiceManager;
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

        $formControlStrategy = $this->formControlServiceManager->getByApplicationStep($applicationStepEntity);

        $selfservePage = $this->selfservePageFactory->create(
            $applicationStepEntity->getQuestion()->getActiveQuestionText()->getTranslationKeyFromQuestionKey(),
            $qaEntity->getAdditionalQaViewData($applicationStepEntity),
            $this->applicationStepGenerator->generate($qaContext),
            $formControlStrategy->getQuestionText($qaContext),
            $applicationStepEntity->getNextStepSlug()
        );

        return $selfservePage;
    }
}

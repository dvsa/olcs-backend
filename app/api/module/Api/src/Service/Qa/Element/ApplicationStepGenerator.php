<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;

class ApplicationStepGenerator
{
    /** @var FormControlStrategyProvider */
    private $formControlStrategyProvider;

    /** @var ApplicationStepFactory */
    private $applicationStepFactory;

    /** @var AnswerRepository */
    private $answerRepo;

    /** @var ValidatorListGenerator */
    private $validatorListGenerator;

    /**
     * Create service instance
     *
     * @param FormControlStrategyProvider $formControlStrategyProvider
     * @param ApplicationStepFactory $applicationStepFactory
     * @param AnswerRepository $answerRepo
     * @param ValidatorListGenerator $validatorListGenerator
     *
     * @return ApplicationStepGenerator
     */
    public function __construct(
        FormControlStrategyProvider $formControlStrategyProvider,
        ApplicationStepFactory $applicationStepFactory,
        AnswerRepository $answerRepo,
        ValidatorListGenerator $validatorListGenerator
    ) {
        $this->formControlStrategyProvider = $formControlStrategyProvider;
        $this->applicationStepFactory = $applicationStepFactory;
        $this->answerRepo = $answerRepo;
        $this->validatorListGenerator = $validatorListGenerator;
    }

    /**
     * Build and return an ApplicationStep instance using the appropriate data sources
     *
     * @param ApplicationStepEntity $applicationStepEntity
     * @param IrhpApplicationEntity $irhpApplicationEntity
     *
     * @return ApplicationStep
     */
    public function generate(ApplicationStepEntity $applicationStepEntity, IrhpApplicationEntity $irhpApplicationEntity)
    {
        try {
            $answerEntity = $this->answerRepo->fetchByQuestionIdAndIrhpApplicationId(
                $applicationStepEntity->getQuestion()->getId(),
                $irhpApplicationEntity->getId()
            );
        } catch (NotFoundException $e) {
            $answerEntity = null;
        }

        $formControlStrategy = $this->formControlStrategyProvider->get($applicationStepEntity);

        return $this->applicationStepFactory->create(
            $formControlStrategy->getFrontendType(),
            $applicationStepEntity->getFieldsetName(),
            $formControlStrategy->getElement($applicationStepEntity, $irhpApplicationEntity, $answerEntity),
            $this->validatorListGenerator->generate($applicationStepEntity)
        );
    }
}

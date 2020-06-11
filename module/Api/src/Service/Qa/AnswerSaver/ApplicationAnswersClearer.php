<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStepsProvider;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use RuntimeException;

class ApplicationAnswersClearer
{
    /** @var SupplementedApplicationStepsProvider */
    private $supplementedApplicationStepsProvider;

    /** @var QaContextFactory */
    private $qaContextFactory;

    /**
     * Create service instance
     *
     * @param SupplementedApplicationStepsProvider $supplementedApplicationStepsProvider
     * @param QaContextFactory $qaContextFactory
     *
     * @return ApplicationAnswersClearer
     */
    public function __construct(
        SupplementedApplicationStepsProvider $supplementedApplicationStepsProvider,
        QaContextFactory $qaContextFactory
    ) {
        $this->supplementedApplicationStepsProvider = $supplementedApplicationStepsProvider;
        $this->qaContextFactory = $qaContextFactory;
    }

    /**
     * Remove or reset to the default state all answers for this application
     *
     * @param QaEntityInterface $qaEntity
     */
    public function clear(QaEntityInterface $qaEntity)
    {
        $supplementedApplicationSteps = $this->supplementedApplicationStepsProvider->get($qaEntity);

        foreach ($supplementedApplicationSteps as $supplementedApplicationStep) {
            $this->clearAnswer($qaEntity, $supplementedApplicationStep);
        }
    }

    /**
     * Remove or reset to the default state all answers for this application that follow the application step
     * contained in the specified qa entity
     *
     * @param QaContext $qaContext
     *
     * @throws RuntimeException
     */
    public function clearAfterApplicationStep(QaContext $qaContext)
    {
        $qaEntity = $qaContext->getQaEntity();
        $applicationStep = $qaContext->getApplicationStepEntity();

        $supplementedApplicationSteps = $this->supplementedApplicationStepsProvider->get($qaEntity);
        $afterApplicationStep = false;

        foreach ($supplementedApplicationSteps as $supplementedApplicationStep) {
            if ($afterApplicationStep) {
                $this->clearAnswer($qaEntity, $supplementedApplicationStep);
            }

            if ($supplementedApplicationStep->getApplicationStep() === $applicationStep) {
                $afterApplicationStep = true;
            }
        }

        if (!$afterApplicationStep) {
            $exceptionMessage = sprintf(
                'application step with id %s was not found in application steps',
                $applicationStep->getId()
            );

            throw new RuntimeException($exceptionMessage);
        }
    }

    /**
     * Clear the answer corresponding to the specified qa entity and supplemented application step
     *
     * @param QaEntityInterface $qaEntity
     * @param SupplementedApplicationStep $supplementedApplicationStep
     */
    private function clearAnswer(QaEntityInterface $qaEntity, SupplementedApplicationStep $supplementedApplicationStep)
    {
        $qaContext = $this->qaContextFactory->create(
            $supplementedApplicationStep->getApplicationStep(),
            $qaEntity
        );

        $supplementedApplicationStep->getFormControlStrategy()->clearAnswer($qaContext);
    }
}

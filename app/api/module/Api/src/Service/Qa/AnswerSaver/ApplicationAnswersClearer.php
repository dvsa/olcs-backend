<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStepsProvider;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;

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
            $qaContext = $this->qaContextFactory->create(
                $supplementedApplicationStep->getApplicationStep(),
                $qaEntity
            );

            $supplementedApplicationStep->getFormControlStrategy()->clearAnswer($qaContext);
        }
    }
}

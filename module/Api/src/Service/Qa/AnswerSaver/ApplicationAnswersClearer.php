<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStepsProvider;

class ApplicationAnswersClearer
{
    /** @var SupplementedApplicationStepsProvider */
    private $supplementedApplicationStepsProvider;

    /**
     * Create service instance
     *
     * @param SupplementedApplicationStepsProvider $supplementedApplicationStepsProvider
     *
     * @return ApplicationAnswersClearer
     */
    public function __construct(SupplementedApplicationStepsProvider $supplementedApplicationStepsProvider)
    {
        $this->supplementedApplicationStepsProvider = $supplementedApplicationStepsProvider;
    }

    /**
     * Remove or reset to the default state all answers for this application
     *
     * @param IrhpApplication $irhpApplication
     */
    public function clear(IrhpApplication $irhpApplication)
    {
        $supplementedApplicationSteps = $this->supplementedApplicationStepsProvider->get($irhpApplication);

        foreach ($supplementedApplicationSteps as $supplementedApplicationStep) {
            $supplementedApplicationStep->getFormControlStrategy()->clearAnswer(
                $supplementedApplicationStep->getApplicationStep(),
                $irhpApplication
            );
        }
    }
}

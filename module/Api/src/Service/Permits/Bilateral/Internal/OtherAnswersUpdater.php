<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

class OtherAnswersUpdater
{
    /** @var GenericAnswerUpdater */
    private $genericAnswerUpdater;

    /** @var ApplicationPathAnswersUpdaterProvider */
    private $applicationPathAnswersUpdaterProvider;

    /**
     * Create service instance
     *
     * @param GenericAnswerUpdater $genericAnswerUpdater
     * @param ApplicationPathAnswersUpdaterProvider $applicationPathAnswersUpdaterProvider
     *
     * @return OtherAnswersUpdater
     */
    public function __construct(
        GenericAnswerUpdater $genericAnswerUpdater,
        ApplicationPathAnswersUpdaterProvider $applicationPathAnswersUpdaterProvider
    ) {
        $this->genericAnswerUpdater = $genericAnswerUpdater;
        $this->applicationPathAnswersUpdaterProvider = $applicationPathAnswersUpdaterProvider;
    }

    /**
     * Update answers other than that representing the number of permits
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param array $bilateralRequired
     * @param string $permitUsageSelection
     */
    public function update(
        IrhpPermitApplication $irhpPermitApplication,
        array $bilateralRequired,
        $permitUsageSelection
    ) {
        $this->genericAnswerUpdater->update(
            $irhpPermitApplication,
            Question::QUESTION_ID_BILATERAL_PERMIT_USAGE,
            $permitUsageSelection
        );

        $applicationPathGroupId = $irhpPermitApplication->getActiveApplicationPath()
            ->getApplicationPathGroup()
            ->getId();

        $applicationPathAnswersUpdater = $this->applicationPathAnswersUpdaterProvider
            ->getByApplicationPathGroupId($applicationPathGroupId);

        $applicationPathAnswersUpdater->update($irhpPermitApplication, $bilateralRequired);

        $irhpPermitApplication->updateCheckAnswers();
    }
}

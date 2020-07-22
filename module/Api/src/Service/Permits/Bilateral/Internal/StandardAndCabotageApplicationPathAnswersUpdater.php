<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

class StandardAndCabotageApplicationPathAnswersUpdater implements ApplicationPathAnswersUpdaterInterface
{
    /** @var GenericAnswerUpdater */
    private $genericAnswerUpdater;

    /**
     * Create service instance
     *
     * @param GenericAnswerUpdater $genericAnswerUpdater
     *
     * @return StandardAndCabotageApplicationPathAnswersUpdater
     */
    public function __construct(GenericAnswerUpdater $genericAnswerUpdater)
    {
        $this->genericAnswerUpdater = $genericAnswerUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function update(IrhpPermitApplication $irhpPermitApplication, array $bilateralRequired)
    {
        $requiredStandard = $bilateralRequired[IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED];
        $requiredCabotage = $bilateralRequired[IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED];

        if ($requiredStandard && $requiredCabotage) {
            $answer = Answer::BILATERAL_STANDARD_AND_CABOTAGE;
        } elseif ($requiredStandard) {
            $answer = Answer::BILATERAL_STANDARD_ONLY;
        } else {
            $answer = Answer::BILATERAL_CABOTAGE_ONLY;
        }

        $this->genericAnswerUpdater->update(
            $irhpPermitApplication,
            Question::QUESTION_ID_BILATERAL_STANDARD_AND_CABOTAGE,
            $answer
        );
    }
}

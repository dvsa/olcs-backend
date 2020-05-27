<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;

class CabotageAnswerUpdater
{
    /** @var QaContextFactory */
    private $qaContextFactory;

    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /**
     * Create service instance
     *
     * @param QaContextFactory $qaContextFactory
     * @param GenericAnswerWriter $genericAnswerWriter
     *
     * @return CabotageAnswerUpdater
     */
    public function __construct(QaContextFactory $qaContextFactory, GenericAnswerWriter $genericAnswerWriter)
    {
        $this->qaContextFactory = $qaContextFactory;
        $this->genericAnswerWriter = $genericAnswerWriter;
    }

    /**
     * Create or update the answer value representing the permit usage selection
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param array $bilateralRequired
     */
    public function update(IrhpPermitApplication $irhpPermitApplication, array $bilateralRequired)
    {
        $questionId = null;
        $answer = null;

        $applicationPath = $irhpPermitApplication->getActiveApplicationPath();

        $applicationPathGroup = $applicationPath->getApplicationPathGroup();
        if ($applicationPathGroup->isBilateralCabotageOnly()) {
            $questionId = Question::QUESTION_ID_BILATERAL_CABOTAGE_ONLY;
            $answer = Answer::BILATERAL_CABOTAGE_ONLY;
        } elseif ($applicationPathGroup->isBilateralStandardAndCabotage()) {
            $questionId = Question::QUESTION_ID_BILATERAL_STANDARD_AND_CABOTAGE;

            $requiredStandard = $bilateralRequired[IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED];
            $requiredCabotage = $bilateralRequired[IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED];

            if ($requiredStandard && $requiredCabotage) {
                $answer = Answer::BILATERAL_STANDARD_AND_CABOTAGE;
            } elseif ($requiredStandard) {
                $answer = Answer::BILATERAL_STANDARD_ONLY;
            } else {
                $answer = Answer::BILATERAL_CABOTAGE_ONLY;
            }
        }

        if (!is_null($questionId)) {
            $qaContext = $this->qaContextFactory->create(
                $applicationPath->getApplicationStepByQuestionId($questionId),
                $irhpPermitApplication
            );

            $this->genericAnswerWriter->write($qaContext, $answer, Question::QUESTION_TYPE_STRING);
        }
    }
}

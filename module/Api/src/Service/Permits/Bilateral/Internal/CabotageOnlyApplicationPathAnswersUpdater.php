<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

class CabotageOnlyApplicationPathAnswersUpdater implements ApplicationPathAnswersUpdaterInterface
{
    /** @var GenericAnswerUpdater */
    private $genericAnswerUpdater;

    /**
     * Create service instance
     *
     * @param GenericAnswerUpdater $genericAnswerUpdater
     *
     * @return CabotageOnlyApplicationPathAnswersUpdater
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
        $this->genericAnswerUpdater->update(
            $irhpPermitApplication,
            Question::QUESTION_ID_BILATERAL_CABOTAGE_ONLY,
            Answer::BILATERAL_CABOTAGE_ONLY
        );
    }
}

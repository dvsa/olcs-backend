<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\EmissionsStandardsAnswerSaver;

class UkraineApplicationPathAnswersUpdater implements ApplicationPathAnswersUpdaterInterface
{
    /** @var GenericAnswerUpdater */
    private $genericAnswerUpdater;

    /**
     * Create service instance
     *
     * @param GenericAnswerUpdater $genericAnswerUpdater
     *
     * @return UkraineApplicationPathAnswersUpdater
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
            Question::QUESTION_ID_BILATERAL_EMISSIONS_STANDARDS,
            EmissionsStandardsAnswerSaver::EURO3_OR_EURO4_ANSWER
        );
    }
}

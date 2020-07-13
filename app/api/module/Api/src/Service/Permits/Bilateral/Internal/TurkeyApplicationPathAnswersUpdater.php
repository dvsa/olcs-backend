<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\ThirdCountryAnswerSaver;

class TurkeyApplicationPathAnswersUpdater implements ApplicationPathAnswersUpdaterInterface
{
    /** @var GenericAnswerUpdater */
    private $genericAnswerUpdater;

    /**
     * Create service instance
     *
     * @param GenericAnswerUpdater $genericAnswerUpdater
     *
     * @return TurkeyApplicationPathAnswersUpdater
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
            Question::QUESTION_ID_BILATERAL_THIRD_COUNTRY,
            ThirdCountryAnswerSaver::YES_ANSWER
        );
    }
}

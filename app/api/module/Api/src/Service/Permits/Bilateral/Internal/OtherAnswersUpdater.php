<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

class OtherAnswersUpdater
{
    /** @var PermitUsageAnswerUpdater */
    private $permitUsageAnswerUpdater;

    /** @var CabotageAnswerUpdater */
    private $cabotageAnswerUpdater;

    /**
     * Create service instance
     *
     * @param PermitUsageAnswerUpdater $permitUsageAnswerUpdater
     * @param CabotageAnswerUpdater $cabotageAnswerUpdater
     *
     * @return OtherAnswersUpdater
     */
    public function __construct(
        PermitUsageAnswerUpdater $permitUsageAnswerUpdater,
        CabotageAnswerUpdater $cabotageAnswerUpdater
    ) {
        $this->permitUsageAnswerUpdater = $permitUsageAnswerUpdater;
        $this->cabotageAnswerUpdater = $cabotageAnswerUpdater;
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
        $this->permitUsageAnswerUpdater->update($irhpPermitApplication, $permitUsageSelection);
        $this->cabotageAnswerUpdater->update($irhpPermitApplication, $bilateralRequired);
        $irhpPermitApplication->updateCheckAnswers();
    }
}

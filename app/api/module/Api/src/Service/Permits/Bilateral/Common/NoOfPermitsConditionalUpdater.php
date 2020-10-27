<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

class NoOfPermitsConditionalUpdater
{
    /** @var NoOfPermitsUpdater */
    private $noOfPermitsUpdater;

    /**
     * Create service instance
     *
     * @param NoOfPermitsUpdater $noOfPermitsUpdater
     *
     * @return NoOfPermitsConditionalUpdater
     */
    public function __construct(NoOfPermitsUpdater $noOfPermitsUpdater)
    {
        $this->noOfPermitsUpdater = $noOfPermitsUpdater;
    }

    /**
     * Update the number of permits answer and fees relating to a specific country within a bilateral application, but
     * only if the number of permits answer has actually changed
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param array $updatedAnswers
     */
    public function update(IrhpPermitApplication $irhpPermitApplication, array $updatedAnswers)
    {
        $existingAnswers = $irhpPermitApplication->getBilateralRequired();

        if ($updatedAnswers == $existingAnswers) {
            return;
        }

        $this->noOfPermitsUpdater->update($irhpPermitApplication, $updatedAnswers);
    }
}

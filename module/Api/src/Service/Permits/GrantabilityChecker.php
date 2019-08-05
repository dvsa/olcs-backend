<?php

namespace Dvsa\Olcs\Api\Service\Permits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\EmissionsCategoryAvailabilityCounter;
use RuntimeException;

class GrantabilityChecker
{
    /** @var EmissionsCategoryAvailabilityCounter */
    private $emissionsCategoryAvailabilityCounter;

    /**
     * Create service instance
     *
     * @param EmissionsCategoryAvailabilityCounter $emissionsCategoryAvailabilityCounter
     *
     * @return EmissionsCategoryAvailabilityChecker
     */
    public function __construct(EmissionsCategoryAvailabilityCounter $emissionsCategoryAvailabilityCounter)
    {
        $this->emissionsCategoryAvailabilityCounter = $emissionsCategoryAvailabilityCounter;
    }

    /**
     * Whether there is sufficient stock to grant the permits required by the application
     *
     * @param IrhpApplication $irhpApplication
     *
     * @return bool
     */
    public function isGrantable(IrhpApplication $irhpApplication)
    {
        if (!$irhpApplication->getIrhpPermitType()->isEcmtShortTerm()) {
            throw new RuntimeException('GrantabilityChecker is only implemented for ecmt short term');
        }

        $irhpPermitApplication = $irhpApplication->getFirstIrhpPermitApplication();
        $irhpPermitStockId = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock()->getId();

        $isEuro5Grantable = $this->isEmissionsCategoryGrantable(
            $irhpPermitStockId,
            RefData::EMISSIONS_CATEGORY_EURO5_REF,
            $irhpPermitApplication->getRequiredEuro5()
        );

        if (!$isEuro5Grantable) {
            return false;
        }

        return $this->isEmissionsCategoryGrantable(
            $irhpPermitStockId,
            RefData::EMISSIONS_CATEGORY_EURO6_REF,
            $irhpPermitApplication->getRequiredEuro6()
        );
    }

    /**
     * Whether there is sufficient stock to grant the required number of permits in a specific emissions category
     *
     * @param int $irhpPermitStockId
     * @param int $emissionsCategoryId
     * @param int $requiredCount
     *
     * @return bool
     */
    private function isEmissionsCategoryGrantable($irhpPermitStockId, $emissionsCategoryId, $requiredCount)
    {
        $availableCount = $this->emissionsCategoryAvailabilityCounter->getCount(
            $irhpPermitStockId,
            $emissionsCategoryId
        );

        return ($requiredCount <= $availableCount);
    }
}

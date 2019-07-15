<?php

namespace Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;

class EmissionsCategoryAvailabilityCounter
{
    /** @var IrhpPermitRangeRepository */
    private $irhpPermitRangeRepo;

    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /** @var IrhpPermitRepository */
    private $irhpPermitRepo;

    /**
     * Create service instance
     *
     * @param IrhpPermitRangeRepository $irhpPermitRangeRepo
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     * @param IrhpPermitRepository $irhpPermitRepo
     *
     * @return EmissionsCategoryAvailabilityCounter
     */
    public function __construct(
        IrhpPermitRangeRepository $irhpPermitRangeRepo,
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo,
        IrhpPermitRepository $irhpPermitRepo
    ) {
        $this->irhpPermitRangeRepo = $irhpPermitRangeRepo;
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
        $this->irhpPermitRepo = $irhpPermitRepo;
    }

    /**
     * Get the count of permits available to apply for within the scope of a specific short term stock and emissions
     * category
     *
     * @param int $irhpPermitStockId
     * @param int $emissionsCategoryId
     *
     * @return int
     */
    public function getCount($irhpPermitStockId, $emissionsCategoryId)
    {
        $combinedRangeSize = $this->irhpPermitRangeRepo->getCombinedRangeSize(
            $irhpPermitStockId,
            $emissionsCategoryId
        );

        if (is_null($combinedRangeSize)) {
            return 0;
        }

        $permitsGranted = $this->irhpPermitApplicationRepo->getRequiredPermitCountWhereApplicationAwaitingPayment(
            $irhpPermitStockId,
            $emissionsCategoryId
        );

        $permitsAllocated = $this->irhpPermitRepo->getPermitCount($irhpPermitStockId, $emissionsCategoryId);

        return ($combinedRangeSize - ($permitsGranted + $permitsAllocated));
    }
}

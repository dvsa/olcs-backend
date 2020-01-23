<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepository;

class EmissionsCategoryAvailabilityCounter
{
    /** @var IrhpPermitRangeRepository */
    private $irhpPermitRangeRepo;

    /** @var IrhpPermitRepository */
    private $irhpPermitRepo;

    /** @var IrhpApplicationRepository */
    private $irhpApplicationRepo;

    /**
     * Create service instance
     *
     * @param IrhpPermitRangeRepository $irhpPermitRangeRepo
     * @param IrhpPermitRepository $irhpPermitRepo
     * @param IrhpApplicationRepository $irhpApplicationRepo
     *
     * @return EmissionsCategoryAvailabilityCounter
     */
    public function __construct(
        IrhpPermitRangeRepository $irhpPermitRangeRepo,
        IrhpPermitRepository $irhpPermitRepo,
        IrhpApplicationRepository $irhpApplicationRepo
    ) {
        $this->irhpPermitRangeRepo = $irhpPermitRangeRepo;
        $this->irhpPermitRepo = $irhpPermitRepo;
        $this->irhpApplicationRepo = $irhpApplicationRepo;
    }

    /**
     * Get the number of available permits of the specified emissions category in the specified stock, taking into
     * account existing assigned permits and in scope candidate permits marked as successful
     *
     * @param int $stockId
     * @param string $emissionsCategoryId
     *
     * @return int
     */
    public function getCount($stockId, $emissionsCategoryId)
    {
        $combinedRangeSize = $this->irhpPermitRangeRepo->getCombinedRangeSize(
            $stockId,
            $emissionsCategoryId
        );

        $allocatedCount = $this->irhpPermitRepo->getPermitCount(
            $stockId,
            $emissionsCategoryId
        );

        $successfulCount = $this->irhpApplicationRepo->getSuccessfulCountInScope(
            $stockId,
            $emissionsCategoryId
        );

        return $combinedRangeSize - ($allocatedCount + $successfulCount);
    }
}

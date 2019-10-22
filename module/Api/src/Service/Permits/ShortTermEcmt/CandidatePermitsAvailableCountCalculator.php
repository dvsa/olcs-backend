<?php

namespace Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;

class CandidatePermitsAvailableCountCalculator
{
    /** @var IrhpCandidatePermitRepository */
    private $irhpCandidatePermitRepo;

    /** @var IrhpPermitRepository */
    private $irhpPermitRepo;

    /**
     * Create service instance
     *
     * @param IrhpCandidatePermitRepository $irhpCandidatePermitRepo
     * @param IrhpPermitRepository $irhpPermitRepo
     *
     * @return CandidatePermitsAvailableCountCalculator
     */
    public function __construct(
        IrhpCandidatePermitRepository $irhpCandidatePermitRepo,
        IrhpPermitRepository $irhpPermitRepo
    ) {
        $this->irhpCandidatePermitRepo = $irhpCandidatePermitRepo;
        $this->irhpPermitRepo = $irhpPermitRepo;
    }

    /**
     * Return the number of permits that would be available for granting in the specified range after taking into account
     * the request for permitsRequired permits. May return a negative number if there is insufficient availability.
     *
     * @param IrhpPermitRange $irhpPermitRange
     * @param int $permitsRequired
     *
     * @return int
     */
    public function getCount(IrhpPermitRange $irhpPermitRange, $permitsRequired)
    {
        $rangeId = $irhpPermitRange->getId();
        $rangeSize = $irhpPermitRange->getSize();

        $issuedCount = $this->irhpPermitRepo->getPermitCountByRange($rangeId);
        $grantedCount = $this->irhpCandidatePermitRepo->fetchCountInRangeWhereApplicationAwaitingFee($rangeId);

        $freePermitsInRange = ($rangeSize - ($issuedCount + $grantedCount));
        return $freePermitsInRange - $permitsRequired;
    }
}

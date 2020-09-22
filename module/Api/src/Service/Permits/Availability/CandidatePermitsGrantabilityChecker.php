<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

class CandidatePermitsGrantabilityChecker
{
    /** @var CandidatePermitsAvailableCountCalculator */
    private $candidatePermitsAvailableCountCalculator;

    /**
     * Create service instance
     *
     * @param CandidatePermitsAvailableCountCalculator $candidatePermitsAvailableCountCalculator
     *
     * @return CandidatePermitsGrantabilityChecker
     */
    public function __construct(CandidatePermitsAvailableCountCalculator $candidatePermitsAvailableCountCalculator)
    {
        $this->candidatePermitsAvailableCountCalculator = $candidatePermitsAvailableCountCalculator;
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
        $ranges = $irhpApplication->getFirstIrhpPermitApplication()
            ->getRangesWithCandidatePermitCounts();

        foreach ($ranges as $range) {
            $freePermitsAfterGrant = $this->candidatePermitsAvailableCountCalculator->getCount(
                $range[IrhpPermitApplication::RANGE_ENTITY_KEY],
                $range[IrhpPermitApplication::REQUESTED_PERMITS_KEY]
            );

            if ($freePermitsAfterGrant < 0) {
                return false;
            }
        }

        return true;
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Permits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\EmissionsCategoriesGrantabilityChecker;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\CandidatePermitsGrantabilityChecker;
use RuntimeException;

class GrantabilityChecker
{
    /** @var EmissionsCategoriesGrantabilityChecker */
    private $emissionsCategoriesGrantabilityChecker;

    /** @var CandidatePermitsGrantabilityChecker */
    private $candidatePermitsGrantabilityChecker;

    /**
     * Create service instance
     *
     * @param EmissionsCategoriesGrantabilityChecker $emissionsCategoriesGrantabilityChecker
     * @param CandidatePermitsGrantabilityChecker $candidatePermitsGrantabilityChecker
     *
     * @return GrantabilityChecker
     */
    public function __construct(
        EmissionsCategoriesGrantabilityChecker $emissionsCategoriesGrantabilityChecker,
        CandidatePermitsGrantabilityChecker $candidatePermitsGrantabilityChecker
    ) {
        $this->emissionsCategoriesGrantabilityChecker = $emissionsCategoriesGrantabilityChecker;
        $this->candidatePermitsGrantabilityChecker = $candidatePermitsGrantabilityChecker;
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

        switch ($irhpApplication->getAllocationMode()) {
            case IrhpPermitStock::ALLOCATION_MODE_EMISSIONS_CATEGORIES:
                return $this->emissionsCategoriesGrantabilityChecker->isGrantable($irhpApplication);
            case IrhpPermitStock::ALLOCATION_MODE_CANDIDATE_PERMITS:
                return $this->candidatePermitsGrantabilityChecker->isGrantable($irhpApplication);
        }

        throw new RuntimeException('Unable to grant application due to unsupported allocation mode');
    }
}

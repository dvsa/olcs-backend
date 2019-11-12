<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Service\Permits\Scoring\CandidatePermitsCreator as ScoringCandidatePermitsCreator;

class IrhpCandidatePermitsCreator
{
    /** @var ScoringCandidatePermitsCreator */
    private $scoringCandidatePermitsCreator;

    /** @var ApggCandidatePermitsCreator */
    private $apggCandidatePermitsCreator;

    /**
     * Create service instance
     *
     * @param ScoringCandidatePermitsCreator $scoringCandidatePermitsCreator
     * @param ApggCandidatePermitsCreator $apggCandidatePermitsCreator
     *
     * @return IrhpCandidatePermitsCreator
     */
    public function __construct(
        ScoringCandidatePermitsCreator $scoringCandidatePermitsCreator,
        ApggCandidatePermitsCreator $apggCandidatePermitsCreator
    ) {
        $this->scoringCandidatePermitsCreator = $scoringCandidatePermitsCreator;
        $this->apggCandidatePermitsCreator = $apggCandidatePermitsCreator;
    }

    /**
     * Create candidate permits as required for the specified application
     *
     * @param IrhpApplication $irhpApplication
     */
    public function createIfRequired(IrhpApplication $irhpApplication)
    {
        $candidatePermitCreationMode = $irhpApplication->getCandidatePermitCreationMode();

        switch ($candidatePermitCreationMode) {
            case IrhpPermitStock::CANDIDATE_MODE_APSG:
                $firstIrhpPermitApplication = $irhpApplication->getFirstIrhpPermitApplication();

                $this->scoringCandidatePermitsCreator->create(
                    $firstIrhpPermitApplication,
                    $firstIrhpPermitApplication->getRequiredEuro5(),
                    $firstIrhpPermitApplication->getRequiredEuro6()
                );
                break;
            case IrhpPermitStock::CANDIDATE_MODE_APGG:
                $this->apggCandidatePermitsCreator->create($irhpApplication);
                break;
        }
    }
}

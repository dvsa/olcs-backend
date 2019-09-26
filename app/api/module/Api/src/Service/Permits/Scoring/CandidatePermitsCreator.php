<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepository;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter as SystemParameterRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;

class CandidatePermitsCreator
{
    /** @var IrhpCandidatePermitRepository */
    private $irhpCandidatePermitRepo;

    /** @var SystemParameterRepository */
    private $systemParameterRepo;

    /** @var IrhpCandidatePermitFactory */
    private $irhpCandidatePermitFactory;

    /**
     * Create service instance
     *
     * @param IrhpCandidatePermitRepository $irhpCandidatePermitRepo
     * @param SystemParameterRepository $systemParameterRepo
     * @param IrhpCandidatePermitFactory $irhpCandidatePermitFactory
     *
     * @return CandidatePermitsCreator
     */
    public function __construct(
        IrhpCandidatePermitRepository $irhpCandidatePermitRepo,
        SystemParameterRepository $systemParameterRepo,
        IrhpCandidatePermitFactory $irhpCandidatePermitFactory
    ) {
        $this->irhpCandidatePermitRepo = $irhpCandidatePermitRepo;
        $this->systemParameterRepo = $systemParameterRepo;
        $this->irhpCandidatePermitFactory = $irhpCandidatePermitFactory;
    }

    /**
     * Create IRHP Candidate Permit records for each emissions category
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param int $requiredEuro5
     * @param int $requiredEuro6
     *
     * @return void
     */
    public function create(
        IrhpPermitApplication $irhpPermitApplication,
        $requiredEuro5 = 0,
        $requiredEuro6 = 0
    ) {
        if ($requiredEuro5 > 0) {
            $this->createIrhpCandidatePermitRecordsForEmissionsCategory(
                $irhpPermitApplication,
                RefData::EMISSIONS_CATEGORY_EURO5_REF,
                $requiredEuro5
            );
        }

        if ($requiredEuro6 > 0) {
            $this->createIrhpCandidatePermitRecordsForEmissionsCategory(
                $irhpPermitApplication,
                RefData::EMISSIONS_CATEGORY_EURO6_REF,
                $requiredEuro6
            );
        }
    }

    /**
     * Create IRHP Candidate Permit records for a given emissions category
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param string $emissionsCategory
     * @param int $permitsRequired
     *
     * @return void
     */
    private function createIrhpCandidatePermitRecordsForEmissionsCategory(
        IrhpPermitApplication $irhpPermitApplication,
        string $emissionsCategory,
        int $permitsRequired
    ) {
        $useAltEcmtIouAlgorithm = $this->systemParameterRepo->fetchValue(
            SystemParameter::USE_ALT_ECMT_IOU_ALGORITHM
        );

        $scoringEmissionsCategory = null;
        if ($useAltEcmtIouAlgorithm) {
            $scoringEmissionsCategory = $emissionsCategory;
        }

        $intensityOfUse = floatval($irhpPermitApplication->getPermitIntensityOfUse($scoringEmissionsCategory));
        $applicationScore = floatval($irhpPermitApplication->getPermitApplicationScore($scoringEmissionsCategory));

        for ($i = 0; $i < $permitsRequired; $i++) {
            $candidatePermit = $this->irhpCandidatePermitFactory->create(
                $irhpPermitApplication,
                $this->irhpCandidatePermitRepo->getRefdataReference($emissionsCategory),
                $intensityOfUse,
                $applicationScore
            );
            $this->irhpCandidatePermitRepo->save($candidatePermit);
        }
    }
}

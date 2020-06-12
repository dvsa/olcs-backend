<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Permits\Allocate\EmissionsStandardCriteriaFactory;

class ApggEmissionsCatCandidatePermitsCreator
{
    /** @var ApggCandidatePermitFactory */
    private $apggCandidatePermitFactory;

    /** @var IrhpCandidatePermitRepository */
    private $irhpCandidatePermitRepo;

    /** @var EmissionsStandardCriteriaFactory */
    private $emissionsStandardCriteriaFactory;

    /**
     * Create service instance
     *
     * @param ApggCandidatePermitFactory $apggCandidatePermitFactory
     * @param IrhpCandidatePermitRepository $irhpCandidatePermitRepo
     * @param EmissionsStandardCriteriaFactory $emissionsStandardCriteriaFactory
     *
     * @return ApggEmissionsCatCandidatePermitsCreator
     */
    public function __construct(
        ApggCandidatePermitFactory $apggCandidatePermitFactory,
        IrhpCandidatePermitRepository $irhpCandidatePermitRepo,
        EmissionsStandardCriteriaFactory $emissionsStandardCriteriaFactory
    ) {
        $this->apggCandidatePermitFactory = $apggCandidatePermitFactory;
        $this->irhpCandidatePermitRepo = $irhpCandidatePermitRepo;
        $this->emissionsStandardCriteriaFactory = $emissionsStandardCriteriaFactory;
    }

    /**
     * Create apgg candidate permits as required for the specified application
     *
     * @param IrhpApplication $irhpApplication
     * @param string $emissionsCategoryId
     */
    public function createIfRequired(IrhpApplication $irhpApplication, $emissionsCategoryId)
    {
        $irhpPermitApplication = $irhpApplication->getFirstIrhpPermitApplication();
        $permitsRequired = $irhpPermitApplication->getRequiredPermitsByEmissionsCategory($emissionsCategoryId);

        if ($permitsRequired > 0) {
            $emissionsStandardCriteria = $this->emissionsStandardCriteriaFactory->create($emissionsCategoryId);

            $irhpPermitRange = $irhpApplication->getAssociatedStock()
                ->getFirstAvailableRangeWithNoCountries($emissionsStandardCriteria);

            for ($index = 0; $index < $permitsRequired; $index++) {
                $irhpCandidatePermit = $this->apggCandidatePermitFactory->create($irhpPermitApplication, $irhpPermitRange);
                $this->irhpCandidatePermitRepo->save($irhpCandidatePermit);
            }
        }
    }
}

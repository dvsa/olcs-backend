<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class ApggEmissionsCatCandidatePermitsCreator
{
    /** @var ApggCandidatePermitFactory */
    private $apggCandidatePermitFactory;

    /** @var IrhpCandidatePermitRepository */
    private $irhpCandidatePermitRepo;

    /**
     * Create service instance
     *
     * @param ApggCandidatePermitFactory $apggCandidatePermitFactory
     * @param IrhpCandidatePermitRepository $irhpCandidatePermitRepo
     *
     * @return ApggEmissionsCatCandidatePermitsCreator
     */
    public function __construct(
        ApggCandidatePermitFactory $apggCandidatePermitFactory,
        IrhpCandidatePermitRepository $irhpCandidatePermitRepo
    ) {
        $this->apggCandidatePermitFactory = $apggCandidatePermitFactory;
        $this->irhpCandidatePermitRepo = $irhpCandidatePermitRepo;
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
            $irhpPermitRange = $irhpApplication->getAssociatedStock()
                ->getFirstAvailableRangeWithNoCountries($emissionsCategoryId);

            for ($index = 0; $index < $permitsRequired; $index++) {
                $irhpCandidatePermit = $this->apggCandidatePermitFactory->create($irhpPermitApplication, $irhpPermitRange);
                $this->irhpCandidatePermitRepo->save($irhpCandidatePermit);
            }
        }
    }
}

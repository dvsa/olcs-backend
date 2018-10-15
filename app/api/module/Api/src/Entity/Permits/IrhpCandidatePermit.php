<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrhpCandidatePermit Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_candidate_permit",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_candidate_permits_irhp_permit_applications1_idx",
     *     columns={"irhp_permit_application_id"}),
 *        @ORM\Index(name="fk_irhp_candidate_permit_irhp_permit_range",
     *     columns={"irhp_permit_range_id"}),
 *        @ORM\Index(name="fk_irhp_candidate_permit_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_candidate_permit_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpCandidatePermit extends AbstractIrhpCandidatePermit
{
    public static function createNew(
        IrhpPermitApplication $irhpPermitApplication,
        float $intensityOfUse = null,
        float $applicationScore = null
    ) {
        $IrhpCandidatePermit = new self();
        $IrhpCandidatePermit->irhpPermitApplication = $irhpPermitApplication;
        $IrhpCandidatePermit->intensityOfUse = $intensityOfUse;
        $IrhpCandidatePermit->applicationScore = $applicationScore;
        $IrhpCandidatePermit->successful = 0;

        return $IrhpCandidatePermit;
    }

    /**
     * Collects data from given candidate permits
     * for use in deviation calculations
     *
     * @param irhpCandidatePermits list of irhp candidate permits to collate information from
     *
     * @return array containing data relevant to Deviation calculations as well as the Mean Deviation
     */
    public static function getDeviationData(array $irhpCandidatePermits)
    {
        $licence = [];
        foreach ($irhpCandidatePermits as $irhpCandidatePermit) {
            $irhpPermitApplication = $irhpCandidatePermit->getIrhpPermitApplication();
            $licence[$irhpPermitApplication->getLicence()->getLicNo()][$irhpPermitApplication->getId()] = $irhpPermitApplication->getPermitsRequired();
        }

        return [
            'licenceData' => $licence,
            'meanDeviation' => count($irhpCandidatePermits) / count($licence),
        ];
    }

    /**
     * Calculates the randomised score for this candidate permit
     * using the given deviation data
     *
     * @param deviationData a pre-formatted array of data for use in calculations
     * @return int a randomised statistical value derived from mean deviation and standard deviation
     */
    public function calculateRandomFactor(array $deviationData)
    {
        $standardDeviation = 0;
        $licenceData = $deviationData['licenceData'][$this->getIrhpPermitApplication()->getLicence()->getLicNo()];
        foreach ($licenceData as $applicationPermitsRequired) {
            $standardDeviation += $applicationPermitsRequired;
        }

        return stats_rand_gen_normal($deviationData['meanDeviation'], $standardDeviation);
    }
}

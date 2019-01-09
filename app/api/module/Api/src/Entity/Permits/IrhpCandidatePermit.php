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
        $irhpCandidatePermit = new self();
        $irhpCandidatePermit->irhpPermitApplication = $irhpPermitApplication;
        $irhpCandidatePermit->intensityOfUse = $intensityOfUse;
        $irhpCandidatePermit->applicationScore = $applicationScore;
        $irhpCandidatePermit->successful = 0;
        $irhpCandidatePermit->inScope = 0;

        return $irhpCandidatePermit;
    }

    /**
     * Prepares this candidate permit for use in a scoring run
     */
    public function prepareForScoring()
    {
        $this->successful = 0;
        $this->irhpPermitRange = null;
    }

    /**
     * Indicates if this candidate permit already has a randomized score set
     *
     * @return bool
     */
    public function hasRandomizedScore()
    {
        return !is_null($this->randomizedScore);
    }

    /**
     * Calculates the randomised score and random factor for this candidate permit
     *
     * @param array $deviationData a pre-formatted array of data for use in calculations
     * @param int $licNo the licence number corresponding to the candidate permit
     */
    public function applyRandomizedScore(array $deviationData, $licNo)
    {
        $standardDeviation = 0;
        $licenceData = $deviationData['licenceData'][$licNo];
        foreach ($licenceData as $applicationPermitsRequired) {
            $standardDeviation += $applicationPermitsRequired;
        }

        $randomFactor = stats_rand_gen_normal($deviationData['meanDeviation'], $standardDeviation);

        $this->randomizedScore = $randomFactor * $this->applicationScore;
        $this->randomFactor = $randomFactor;
    }

    /**
     * Sets the range for this candidate permit
     *
     * @param IrhpPermitRange $range the range to be applied
     */
    public function applyRange(IrhpPermitRange $range)
    {
        $this->irhpPermitRange = $range;
    }
}

<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrhpCandidatePermit Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_candidate_permit",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_candidate_permits_irhp_permit_applications1_idx",
     *     columns={"irhp_permit_application_id"}),
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
        IrhpPermitRange $IrhpPermitRange,
        decimal $intensityOfUse = null,
        decimal $randomizedScore = null,
        decimal $applicationScore = null
    ) {
        $IrhpCandidatePermit = new self();
        $IrhpCandidatePermit->irhpPermitApplication = $irhpPermitApplication;
        $IrhpCandidatePermit->irhpPermitRange = $IrhpPermitRange;
        $IrhpCandidatePermit->intensityOfUse = $intensityOfUse;
        $IrhpCandidatePermit->randomizedScore = $randomizedScore;
        $IrhpCandidatePermit->applicationScore = $applicationScore;

        return $IrhpCandidatePermit;
    }
}

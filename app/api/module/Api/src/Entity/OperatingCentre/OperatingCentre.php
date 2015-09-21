<?php

namespace Dvsa\Olcs\Api\Entity\OperatingCentre;

use Doctrine\ORM\Mapping as ORM;

/**
 * OperatingCentre Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="operating_centre",
 *    indexes={
 *        @ORM\Index(name="ix_operating_centre_address_id", columns={"address_id"}),
 *        @ORM\Index(name="ix_operating_centre_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_operating_centre_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_operating_centre_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class OperatingCentre extends AbstractOperatingCentre
{
    protected $hasEnvironmentalComplaint;

    protected $hasOpposition;

    public function getHasEnvironmentalComplaint()
    {
        $this->hasEnvironmentalComplaint = 'N';

        /** @var \Dvsa\Olcs\Api\Entity\Cases\Complaint $complaint */
        foreach ($this->getComplaints() as $complaint) {

            if ($complaint->getClosedDate() === null && $complaint->isEnvironmentalComplaint()) {

                $this->hasEnvironmentalComplaint = 'Y';
                break;
            }
        }

        return $this->hasEnvironmentalComplaint;
    }

    public function getHasOpposition()
    {
        $this->hasOpposition = 'N';

        /** @var \Dvsa\Olcs\Api\Entity\Opposition\Opposition $opposition */
        foreach ($this->getOppositions() as $opposition) {
            // Only move on if is NOT withdrawn.
            if ($opposition->getIsWithdrawn() === false) {

                // Do we even have a linked application??
                if ($application = $opposition->getCase()->getApplication()) {

                    $notAllowedStatuses = ['apsts_granted', 'apsts_refused', 'apsts_ntu', 'apsts_withdrawn'];

                    // status is NONE of these
                    if (!in_array($application->getStatus()->getId(), $notAllowedStatuses)) {

                        $this->hasOpposition = 'Y';
                        break;
                    }
                }
            }
        }

        return $this->hasOpposition;
    }
}

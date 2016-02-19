<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Venue as VenueEntity;

/**
 * Impounding Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="impounding",
 *    indexes={
 *        @ORM\Index(name="ix_impounding_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_impounding_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_impounding_presiding_tc_id", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="ix_impounding_outcome", columns={"outcome"}),
 *        @ORM\Index(name="ix_impounding_impounding_type", columns={"impounding_type"}),
 *        @ORM\Index(name="ix_impounding_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_impounding_venue_id", columns={"venue_id"})
 *    }
 * )
 */
class Impounding extends AbstractImpounding
{
    const VENUE_OTHER = 'other';

    public function __construct(Cases $case, RefData $impoundingType)
    {
        parent::__construct();

        $this->setCase($case);
        $this->setImpoundingType($impoundingType);
    }

    /**
     * Sets the venue and venueOther properties, since they are interdependent on each other.
     *
     * @param VenueEntity $venue
     * @param $venueOther
     * @return Impounding
     */
    public function setVenueProperties($venue, $venueOther = null)
    {
        if (empty($venue)) {
            $this->venue = null;
            $this->venueOther = null;
        } else {
            if ($venue === self::VENUE_OTHER) {
                $this->venue = null;
                $this->venueOther = $venueOther;
            } else {
                $this->venue = $venue;
                $this->venueOther = null;
            }
        }

        return $this;
    }
}

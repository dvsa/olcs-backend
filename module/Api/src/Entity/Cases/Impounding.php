<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;

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
 *        @ORM\Index(name="ix_impounding_pi_venue_id", columns={"pi_venue_id"})
 *    }
 * )
 */
class Impounding extends AbstractImpounding
{
    const PI_VENUE_OTHER = 'other';

    public function __construct(Cases $case, RefData $impoundingType)
    {
        parent::__construct();

        $this->setCase($case);
        $this->setImpoundingType($impoundingType);
    }

    /**
     * Sets the piVenue and piVenueOther properties, since they are interdependent on each other.
     *
     * @param RefData $piVenue
     * @param RefData $piVenueOther
     * @return Impounding
     */
    public function setPiVenueProperties(RefData $piVenue, RefData $piVenueOther)
    {
        if ($piVenue != self::PI_VENUE_OTHER) {
            $this->piVenueOther = null;
        } else {
            $this->piVenueOther = $piVenueOther;
        }

        $this->piVenue = $piVenue;

        return $this;
    }
}

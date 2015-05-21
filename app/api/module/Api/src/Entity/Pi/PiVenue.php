<?php

namespace Dvsa\Olcs\Api\Entity\Pi;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiVenue Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="pi_venue",
 *    indexes={
 *        @ORM\Index(name="ix_pi_venue_address_id", columns={"address_id"}),
 *        @ORM\Index(name="ix_pi_venue_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_pi_venue_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_pi_venue_traffic_area_id", columns={"traffic_area_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_pi_venue_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class PiVenue extends AbstractPiVenue
{

}

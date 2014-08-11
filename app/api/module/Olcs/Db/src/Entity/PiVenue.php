<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PiVenue Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pi_venue",
 *    indexes={
 *        @ORM\Index(name="fk_pi_venue_address1_idx", columns={"address_id"}),
 *        @ORM\Index(name="fk_pi_venue_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_venue_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_pi_venue_traffic_area1_idx", columns={"traffic_area_id"})
 *    }
 * )
 */
class PiVenue implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\TrafficAreaManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\AddressManyToOne,
        Traits\Name70Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

}

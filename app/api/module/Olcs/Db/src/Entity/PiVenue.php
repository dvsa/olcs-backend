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
class PiVenue implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\AddressManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Name70Field,
        Traits\OlbsKeyField,
        Traits\TrafficAreaManyToOneAlt1,
        Traits\CustomVersionField;
}

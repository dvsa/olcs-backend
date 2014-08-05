<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * OperatingCentre Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="operating_centre",
 *    indexes={
 *        @ORM\Index(name="fk_OperatingCentre_Address1_idx", columns={"address_id"}),
 *        @ORM\Index(name="fk_operating_centre_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_operating_centre_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class OperatingCentre implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\AddressManyToOne,
        Traits\ViAction1Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}

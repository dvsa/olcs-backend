<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * BusRegLocalAuth Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bus_reg_local_auth",
 *    indexes={
 *        @ORM\Index(name="fk_bus_reg_local_auth_local_authority1_idx", columns={"local_authority_id"}),
 *        @ORM\Index(name="fk_bus_reg_local_auth_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_bus_reg_local_auth_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_B1BB6E195327B2E3", columns={"bus_reg_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="bus_reg_id", columns={"bus_reg_id","local_authority_id"})
 *    }
 * )
 */
class BusRegLocalAuth implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LocalAuthorityManyToOne,
        Traits\BusRegManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

}

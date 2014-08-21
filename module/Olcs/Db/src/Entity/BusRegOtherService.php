<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * BusRegOtherService Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bus_reg_other_service",
 *    indexes={
 *        @ORM\Index(name="fk_bus_reg_other_service_bus_reg1_idx", columns={"bus_reg_id"}),
 *        @ORM\Index(name="fk_bus_reg_other_service_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_bus_reg_other_service_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class BusRegOtherService implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\BusRegManyToOneAlt1,
        Traits\ServiceNo70Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

}

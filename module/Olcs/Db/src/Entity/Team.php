<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Team Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="team",
 *    indexes={
 *        @ORM\Index(name="fk_team_traffic_area1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_team_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_team_user1_idx", columns={"created_by"})
 *    }
 * )
 */
class Team implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\Description255FieldAlt1,
        Traits\Name70Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}

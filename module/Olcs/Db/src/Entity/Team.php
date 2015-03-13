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
 *        @ORM\Index(name="ix_team_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_team_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_team_created_by", columns={"created_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_team_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Team implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255FieldAlt1,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Name70Field,
        Traits\OlbsKeyField,
        Traits\TrafficAreaManyToOne,
        Traits\CustomVersionField;
}

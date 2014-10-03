<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * OperatingCentreOpposition Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="operating_centre_opposition",
 *    indexes={
 *        @ORM\Index(name="IDX_14273D70DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_14273D70B4BE57B7", columns={"opposition_id"}),
 *        @ORM\Index(name="IDX_14273D7065CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_14273D7035382CCB", columns={"operating_centre_id"})
 *    }
 * )
 */
class OperatingCentreOpposition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\OppositionManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}

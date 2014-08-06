<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Penalty Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="penalty",
 *    indexes={
 *        @ORM\Index(name="IDX_AFE28FD8915C14AD", columns={"case_id"}),
 *        @ORM\Index(name="IDX_AFE28FD8DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_AFE28FD865CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class Penalty implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\NotesField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}

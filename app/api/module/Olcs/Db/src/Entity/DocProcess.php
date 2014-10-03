<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * DocProcess Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="doc_process",
 *    indexes={
 *        @ORM\Index(name="IDX_59FF9048DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_59FF904865CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_59FF904812469DE2", columns={"category_id"})
 *    }
 * )
 */
class DocProcess implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CategoryManyToOne,
        Traits\Description255Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}

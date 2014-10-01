<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PublicationSection Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="publication_section",
 *    indexes={
 *        @ORM\Index(name="IDX_25FAACD365CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_25FAACD3DE12AB56", columns={"created_by"})
 *    }
 * )
 */
class PublicationSection implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description70Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}

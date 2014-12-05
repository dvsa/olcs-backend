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
 *        @ORM\Index(name="fk_publication_section_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_publication_section_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class PublicationSection implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description70Field,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}

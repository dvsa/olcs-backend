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
 *        @ORM\Index(name="fk_doc_process_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_doc_process_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_doc_process_document_category1_idx", columns={"category_id"})
 *    }
 * )
 */
class DocProcess implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CategoryManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description255Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

}

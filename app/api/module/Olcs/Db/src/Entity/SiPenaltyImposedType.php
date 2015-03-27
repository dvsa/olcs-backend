<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SiPenaltyImposedType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="si_penalty_imposed_type",
 *    indexes={
 *        @ORM\Index(name="ix_si_penalty_imposed_type_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_si_penalty_imposed_type_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class SiPenaltyImposedType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255FieldAlt1,
        Traits\Id8Identity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return SiPenaltyImposedType
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }
}

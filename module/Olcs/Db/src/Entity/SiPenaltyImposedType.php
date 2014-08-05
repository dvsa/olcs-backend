<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * SiPenaltyImposedType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="si_penalty_imposed_type",
 *    indexes={
 *        @ORM\Index(name="fk_si_penalty_imposed_type_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_si_penalty_imposed_type_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class SiPenaltyImposedType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Id8Identity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description255FieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Removed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="removed_date", nullable=true)
     */
    protected $removedDate;

    /**
     * Set the removed date
     *
     * @param \DateTime $removedDate
     * @return \Olcs\Db\Entity\SiPenaltyImposedType
     */
    public function setRemovedDate($removedDate)
    {
        $this->removedDate = $removedDate;

        return $this;
    }

    /**
     * Get the removed date
     *
     * @return \DateTime
     */
    public function getRemovedDate()
    {
        return $this->removedDate;
    }
}

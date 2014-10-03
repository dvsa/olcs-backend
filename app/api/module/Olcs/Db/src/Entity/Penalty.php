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
 *        @ORM\Index(name="IDX_AFE28FD8DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_AFE28FD8CF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_AFE28FD865CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class Penalty implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\LastModifiedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="text", name="notes", nullable=true)
     */
    protected $notes;

    /**
     * Set the notes
     *
     * @param string $notes
     * @return Penalty
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get the notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
}

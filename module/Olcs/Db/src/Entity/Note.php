<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Note Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="note",
 *    indexes={
 *        @ORM\Index(name="ix_note_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_note_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_note_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_note_irfo_gv_permit_id", columns={"irfo_gv_permit_id"}),
 *        @ORM\Index(name="ix_note_irfo_psv_auth_id", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="ix_note_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_note_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_note_note_type", columns={"note_type"}),
 *        @ORM\Index(name="ix_note_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="fk_note_transport_manager1_idx", columns={"transport_manager_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_note_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Note implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ApplicationManyToOne,
        Traits\BusRegManyToOneAlt1,
        Traits\CaseManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\IrfoGvPermitManyToOne,
        Traits\IrfoPsvAuthManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOneAlt1,
        Traits\OlbsKeyField,
        Traits\OlbsType32Field,
        Traits\TransportManagerManyToOne,
        Traits\CustomVersionField;

    /**
     * Comment
     *
     * @var string
     *
     * @ORM\Column(type="string", name="comment", length=4000, nullable=false)
     */
    protected $comment;

    /**
     * Note type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="note_type", referencedColumnName="id", nullable=false)
     */
    protected $noteType;

    /**
     * Priority
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="priority", nullable=false, options={"default": 0})
     */
    protected $priority = 0;

    /**
     * Set the comment
     *
     * @param string $comment
     * @return Note
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the note type
     *
     * @param \Olcs\Db\Entity\RefData $noteType
     * @return Note
     */
    public function setNoteType($noteType)
    {
        $this->noteType = $noteType;

        return $this;
    }

    /**
     * Get the note type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getNoteType()
    {
        return $this->noteType;
    }

    /**
     * Set the priority
     *
     * @param string $priority
     * @return Note
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get the priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }
}

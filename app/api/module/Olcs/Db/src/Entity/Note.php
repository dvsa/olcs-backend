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
 *        @ORM\Index(name="fk_note_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_note_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_note_case1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_note_irfo_gv_permit1_idx", columns={"irfo_gv_permit_id"}),
 *        @ORM\Index(name="fk_note_irfo_psv_auth1_idx", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="fk_note_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_note_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_note_ref_data1_idx", columns={"note_type"})
 *    }
 * )
 */
class Note implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\IrfoPsvAuthManyToOne,
        Traits\IrfoGvPermitManyToOne,
        Traits\LicenceManyToOne,
        Traits\CaseManyToOne,
        Traits\ApplicationManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Note type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="note_type", referencedColumnName="id")
     */
    protected $noteType;

    /**
     * Comment
     *
     * @var string
     *
     * @ORM\Column(type="string", name="comment", length=4000, nullable=false)
     */
    protected $comment;

    /**
     * Priority
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="priority", nullable=false)
     */
    protected $priority = 0;

    /**
     * Set the note type
     *
     * @param \Olcs\Db\Entity\RefData $noteType
     * @return \Olcs\Db\Entity\Note
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
     * Set the comment
     *
     * @param string $comment
     * @return \Olcs\Db\Entity\Note
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
     * Set the priority
     *
     * @param boolean $priority
     * @return \Olcs\Db\Entity\Note
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get the priority
     *
     * @return boolean
     */
    public function getPriority()
    {
        return $this->priority;
    }
}

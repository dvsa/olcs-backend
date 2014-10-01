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
 *        @ORM\Index(name="IDX_CFBDFA142CA44671", columns={"note_type"}),
 *        @ORM\Index(name="IDX_CFBDFA1465CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_CFBDFA145327B2E3", columns={"bus_reg_id"}),
 *        @ORM\Index(name="IDX_CFBDFA14DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_CFBDFA144425C407", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="IDX_CFBDFA1426EF07C9", columns={"licence_id"}),
 *        @ORM\Index(name="IDX_CFBDFA14CF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_CFBDFA145B05B235", columns={"irfo_gv_permit_id"}),
 *        @ORM\Index(name="IDX_CFBDFA143E030ACD", columns={"application_id"})
 *    }
 * )
 */
class Note implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CaseManyToOne,
        Traits\CreatedByManyToOne,
        Traits\IrfoGvPermitManyToOne,
        Traits\BusRegManyToOneAlt1,
        Traits\LicenceManyToOneAlt1,
        Traits\IrfoPsvAuthManyToOne,
        Traits\ApplicationManyToOneAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Note type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="note_type", referencedColumnName="id", nullable=false)
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
     * @var string
     *
     * @ORM\Column(type="yesno", name="priority", nullable=false)
     */
    protected $priority;

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

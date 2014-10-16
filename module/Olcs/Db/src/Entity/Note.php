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
 *        @ORM\Index(name="fk_note_ref_data1_idx", columns={"note_type"}),
 *        @ORM\Index(name="fk_note_bus_reg1_idx", columns={"bus_reg_id"})
 *    }
 * )
 */
class Note implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceManyToOneAlt1,
        Traits\CaseManyToOne,
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
     * Bus reg
     *
     * @var \Olcs\Db\Entity\BusReg
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\BusReg", fetch="LAZY")
     * @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id", nullable=true)
     */
    protected $busReg;

    /**
     * Irfo psv auth
     *
     * @var \Olcs\Db\Entity\IrfoPsvAuth
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoPsvAuth", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_psv_auth_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoPsvAuth;

    /**
     * Irfo gv permit
     *
     * @var \Olcs\Db\Entity\IrfoGvPermit
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoGvPermit", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_gv_permit_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoGvPermit;

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
    protected $priority = 0;

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
     * Set the bus reg
     *
     * @param \Olcs\Db\Entity\BusReg $busReg
     * @return Note
     */
    public function setBusReg($busReg)
    {
        $this->busReg = $busReg;

        return $this;
    }

    /**
     * Get the bus reg
     *
     * @return \Olcs\Db\Entity\BusReg
     */
    public function getBusReg()
    {
        return $this->busReg;
    }

    /**
     * Set the irfo psv auth
     *
     * @param \Olcs\Db\Entity\IrfoPsvAuth $irfoPsvAuth
     * @return Note
     */
    public function setIrfoPsvAuth($irfoPsvAuth)
    {
        $this->irfoPsvAuth = $irfoPsvAuth;

        return $this;
    }

    /**
     * Get the irfo psv auth
     *
     * @return \Olcs\Db\Entity\IrfoPsvAuth
     */
    public function getIrfoPsvAuth()
    {
        return $this->irfoPsvAuth;
    }

    /**
     * Set the irfo gv permit
     *
     * @param \Olcs\Db\Entity\IrfoGvPermit $irfoGvPermit
     * @return Note
     */
    public function setIrfoGvPermit($irfoGvPermit)
    {
        $this->irfoGvPermit = $irfoGvPermit;

        return $this;
    }

    /**
     * Get the irfo gv permit
     *
     * @return \Olcs\Db\Entity\IrfoGvPermit
     */
    public function getIrfoGvPermit()
    {
        return $this->irfoGvPermit;
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

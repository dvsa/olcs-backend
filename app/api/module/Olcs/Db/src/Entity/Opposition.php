<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Opposition Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="opposition",
 *    indexes={
 *        @ORM\Index(name="fk_opposition_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_opposition_opposer1_idx", columns={"opposer_id"}),
 *        @ORM\Index(name="fk_opposition_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_opposition_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Opposition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\ApplicationManyToOne,
        Traits\Notes4000Field,
        Traits\DeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Opposer
     *
     * @var \Olcs\Db\Entity\Opposer
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Opposer")
     * @ORM\JoinColumn(name="opposer_id", referencedColumnName="id")
     */
    protected $opposer;

    /**
     * Is representation
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_representation", nullable=false)
     */
    protected $isRepresentation;

    /**
     * Is copied
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_copied", nullable=false)
     */
    protected $isCopied = 0;

    /**
     * Raised date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="raised_date", nullable=true)
     */
    protected $raisedDate;

    /**
     * Is in time
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_in_time", nullable=false)
     */
    protected $isInTime = 0;

    /**
     * Is public inquiry
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_public_inquiry", nullable=false)
     */
    protected $isPublicInquiry = 0;

    /**
     * Is valid
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_valid", nullable=false)
     */
    protected $isValid;

    /**
     * Valid notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="valid_notes", length=4000, nullable=true)
     */
    protected $validNotes;

    /**
     * Set the opposer
     *
     * @param \Olcs\Db\Entity\Opposer $opposer
     * @return \Olcs\Db\Entity\Opposition
     */
    public function setOpposer($opposer)
    {
        $this->opposer = $opposer;

        return $this;
    }

    /**
     * Get the opposer
     *
     * @return \Olcs\Db\Entity\Opposer
     */
    public function getOpposer()
    {
        return $this->opposer;
    }

    /**
     * Set the is representation
     *
     * @param boolean $isRepresentation
     * @return \Olcs\Db\Entity\Opposition
     */
    public function setIsRepresentation($isRepresentation)
    {
        $this->isRepresentation = $isRepresentation;

        return $this;
    }

    /**
     * Get the is representation
     *
     * @return boolean
     */
    public function getIsRepresentation()
    {
        return $this->isRepresentation;
    }

    /**
     * Set the is copied
     *
     * @param boolean $isCopied
     * @return \Olcs\Db\Entity\Opposition
     */
    public function setIsCopied($isCopied)
    {
        $this->isCopied = $isCopied;

        return $this;
    }

    /**
     * Get the is copied
     *
     * @return boolean
     */
    public function getIsCopied()
    {
        return $this->isCopied;
    }

    /**
     * Set the raised date
     *
     * @param \DateTime $raisedDate
     * @return \Olcs\Db\Entity\Opposition
     */
    public function setRaisedDate($raisedDate)
    {
        $this->raisedDate = $raisedDate;

        return $this;
    }

    /**
     * Get the raised date
     *
     * @return \DateTime
     */
    public function getRaisedDate()
    {
        return $this->raisedDate;
    }

    /**
     * Set the is in time
     *
     * @param boolean $isInTime
     * @return \Olcs\Db\Entity\Opposition
     */
    public function setIsInTime($isInTime)
    {
        $this->isInTime = $isInTime;

        return $this;
    }

    /**
     * Get the is in time
     *
     * @return boolean
     */
    public function getIsInTime()
    {
        return $this->isInTime;
    }

    /**
     * Set the is public inquiry
     *
     * @param boolean $isPublicInquiry
     * @return \Olcs\Db\Entity\Opposition
     */
    public function setIsPublicInquiry($isPublicInquiry)
    {
        $this->isPublicInquiry = $isPublicInquiry;

        return $this;
    }

    /**
     * Get the is public inquiry
     *
     * @return boolean
     */
    public function getIsPublicInquiry()
    {
        return $this->isPublicInquiry;
    }

    /**
     * Set the is valid
     *
     * @param boolean $isValid
     * @return \Olcs\Db\Entity\Opposition
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * Get the is valid
     *
     * @return boolean
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Set the valid notes
     *
     * @param string $validNotes
     * @return \Olcs\Db\Entity\Opposition
     */
    public function setValidNotes($validNotes)
    {
        $this->validNotes = $validNotes;

        return $this;
    }

    /**
     * Get the valid notes
     *
     * @return string
     */
    public function getValidNotes()
    {
        return $this->validNotes;
    }
}

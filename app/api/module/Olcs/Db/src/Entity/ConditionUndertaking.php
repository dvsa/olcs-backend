<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ConditionUndertaking Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="condition_undertaking",
 *    indexes={
 *        @ORM\Index(name="fk_Condition_ref_data1_idx", columns={"added_via"}),
 *        @ORM\Index(name="fk_Condition_ref_data2_idx", columns={"attached_to"}),
 *        @ORM\Index(name="fk_Condition_ref_data3_idx", columns={"condition_type"}),
 *        @ORM\Index(name="fk_Condition_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_Condition_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_Condition_operating_centre1_idx", columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_condition_undertaking_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_condition_undertaking_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_condition_undertaking_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_condition_undertaking_condition_undertaking1_idx", columns={"lic_condition_variation_id"}),
 *        @ORM\Index(name="fk_condition_undertaking_user3_idx", columns={"approval_user_id"})
 *    }
 * )
 */
class ConditionUndertaking implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\ApplicationManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\CaseManyToOne,
        Traits\LicenceManyToOne,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Lic condition variation
     *
     * @var \Olcs\Db\Entity\ConditionUndertaking
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ConditionUndertaking")
     * @ORM\JoinColumn(name="lic_condition_variation_id", referencedColumnName="id")
     */
    protected $licConditionVariation;

    /**
     * Approval user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="approval_user_id", referencedColumnName="id")
     */
    protected $approvalUser;

    /**
     * Attached to
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="attached_to", referencedColumnName="id")
     */
    protected $attachedTo;

    /**
     * Condition type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="condition_type", referencedColumnName="id")
     */
    protected $conditionType;

    /**
     * Added via
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="added_via", referencedColumnName="id")
     */
    protected $addedVia;

    /**
     * Condition date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="condition_date", nullable=false)
     */
    protected $conditionDate;

    /**
     * Is draft
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_draft", nullable=false)
     */
    protected $isDraft = 0;

    /**
     * Is fulfilled
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_fulfilled", nullable=false)
     */
    protected $isFulfilled = 0;

    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=8000, nullable=true)
     */
    protected $notes;

    /**
     * Is approved
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_approved", nullable=false)
     */
    protected $isApproved = 0;


    /**
     * Set the lic condition variation
     *
     * @param \Olcs\Db\Entity\ConditionUndertaking $licConditionVariation
     * @return ConditionUndertaking
     */
    public function setLicConditionVariation($licConditionVariation)
    {
        $this->licConditionVariation = $licConditionVariation;

        return $this;
    }

    /**
     * Get the lic condition variation
     *
     * @return \Olcs\Db\Entity\ConditionUndertaking
     */
    public function getLicConditionVariation()
    {
        return $this->licConditionVariation;
    }


    /**
     * Set the approval user
     *
     * @param \Olcs\Db\Entity\User $approvalUser
     * @return ConditionUndertaking
     */
    public function setApprovalUser($approvalUser)
    {
        $this->approvalUser = $approvalUser;

        return $this;
    }

    /**
     * Get the approval user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getApprovalUser()
    {
        return $this->approvalUser;
    }


    /**
     * Set the attached to
     *
     * @param \Olcs\Db\Entity\RefData $attachedTo
     * @return ConditionUndertaking
     */
    public function setAttachedTo($attachedTo)
    {
        $this->attachedTo = $attachedTo;

        return $this;
    }

    /**
     * Get the attached to
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getAttachedTo()
    {
        return $this->attachedTo;
    }


    /**
     * Set the condition type
     *
     * @param \Olcs\Db\Entity\RefData $conditionType
     * @return ConditionUndertaking
     */
    public function setConditionType($conditionType)
    {
        $this->conditionType = $conditionType;

        return $this;
    }

    /**
     * Get the condition type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getConditionType()
    {
        return $this->conditionType;
    }


    /**
     * Set the added via
     *
     * @param \Olcs\Db\Entity\RefData $addedVia
     * @return ConditionUndertaking
     */
    public function setAddedVia($addedVia)
    {
        $this->addedVia = $addedVia;

        return $this;
    }

    /**
     * Get the added via
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getAddedVia()
    {
        return $this->addedVia;
    }


    /**
     * Set the condition date
     *
     * @param \DateTime $conditionDate
     * @return ConditionUndertaking
     */
    public function setConditionDate($conditionDate)
    {
        $this->conditionDate = $conditionDate;

        return $this;
    }

    /**
     * Get the condition date
     *
     * @return \DateTime
     */
    public function getConditionDate()
    {
        return $this->conditionDate;
    }


    /**
     * Set the is draft
     *
     * @param unknown $isDraft
     * @return ConditionUndertaking
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get the is draft
     *
     * @return unknown
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }


    /**
     * Set the is fulfilled
     *
     * @param unknown $isFulfilled
     * @return ConditionUndertaking
     */
    public function setIsFulfilled($isFulfilled)
    {
        $this->isFulfilled = $isFulfilled;

        return $this;
    }

    /**
     * Get the is fulfilled
     *
     * @return unknown
     */
    public function getIsFulfilled()
    {
        return $this->isFulfilled;
    }


    /**
     * Set the notes
     *
     * @param string $notes
     * @return ConditionUndertaking
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


    /**
     * Set the is approved
     *
     * @param unknown $isApproved
     * @return ConditionUndertaking
     */
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * Get the is approved
     *
     * @return unknown
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }

}

<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
        Traits\Action1Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Added via
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="added_via", referencedColumnName="id", nullable=true)
     */
    protected $addedVia;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="conditionUndertakings")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
     */
    protected $application;

    /**
     * Approval user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="approval_user_id", referencedColumnName="id", nullable=true)
     */
    protected $approvalUser;

    /**
     * Attached to
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="attached_to", referencedColumnName="id", nullable=true)
     */
    protected $attachedTo;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", inversedBy="conditionUndertakings")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=true)
     */
    protected $case;

    /**
     * Condition type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="condition_type", referencedColumnName="id", nullable=false)
     */
    protected $conditionType;

    /**
     * Is approved
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_approved", nullable=false, options={"default": 0})
     */
    protected $isApproved = 0;

    /**
     * Is draft
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_draft", nullable=false, options={"default": 0})
     */
    protected $isDraft = 0;

    /**
     * Is fulfilled
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_fulfilled", nullable=false, options={"default": 0})
     */
    protected $isFulfilled = 0;

    /**
     * Lic condition variation
     *
     * @var \Olcs\Db\Entity\ConditionUndertaking
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ConditionUndertaking", inversedBy="variationRecords")
     * @ORM\JoinColumn(name="lic_condition_variation_id", referencedColumnName="id", nullable=true)
     */
    protected $licConditionVariation;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="conditionUndertakings")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=8000, nullable=true)
     */
    protected $notes;

    /**
     * Operating centre
     *
     * @var \Olcs\Db\Entity\OperatingCentre
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\OperatingCentre", inversedBy="conditionUndertakings")
     * @ORM\JoinColumn(name="operating_centre_id", referencedColumnName="id", nullable=true)
     */
    protected $operatingCentre;

    /**
     * S4
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\S4", inversedBy="conditions")
     * @ORM\JoinTable(name="s4_condition",
     *     joinColumns={
     *         @ORM\JoinColumn(name="condition_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="s4_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $s4s;

    /**
     * Variation record
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ConditionUndertaking", mappedBy="licConditionVariation")
     */
    protected $variationRecords;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->s4s = new ArrayCollection();
        $this->variationRecords = new ArrayCollection();
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
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return ConditionUndertaking
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
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
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return ConditionUndertaking
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
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
     * Set the is approved
     *
     * @param string $isApproved
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
     * @return string
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * Set the is draft
     *
     * @param string $isDraft
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
     * @return string
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set the is fulfilled
     *
     * @param string $isFulfilled
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
     * @return string
     */
    public function getIsFulfilled()
    {
        return $this->isFulfilled;
    }

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
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return ConditionUndertaking
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
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
     * Set the operating centre
     *
     * @param \Olcs\Db\Entity\OperatingCentre $operatingCentre
     * @return ConditionUndertaking
     */
    public function setOperatingCentre($operatingCentre)
    {
        $this->operatingCentre = $operatingCentre;

        return $this;
    }

    /**
     * Get the operating centre
     *
     * @return \Olcs\Db\Entity\OperatingCentre
     */
    public function getOperatingCentre()
    {
        return $this->operatingCentre;
    }

    /**
     * Set the s4
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $s4s
     * @return ConditionUndertaking
     */
    public function setS4s($s4s)
    {
        $this->s4s = $s4s;

        return $this;
    }

    /**
     * Get the s4s
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getS4s()
    {
        return $this->s4s;
    }

    /**
     * Add a s4s
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $s4s
     * @return ConditionUndertaking
     */
    public function addS4s($s4s)
    {
        if ($s4s instanceof ArrayCollection) {
            $this->s4s = new ArrayCollection(
                array_merge(
                    $this->s4s->toArray(),
                    $s4s->toArray()
                )
            );
        } elseif (!$this->s4s->contains($s4s)) {
            $this->s4s->add($s4s);
        }

        return $this;
    }

    /**
     * Remove a s4s
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $s4s
     * @return ConditionUndertaking
     */
    public function removeS4s($s4s)
    {
        if ($this->s4s->contains($s4s)) {
            $this->s4s->removeElement($s4s);
        }

        return $this;
    }

    /**
     * Set the variation record
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $variationRecords
     * @return ConditionUndertaking
     */
    public function setVariationRecords($variationRecords)
    {
        $this->variationRecords = $variationRecords;

        return $this;
    }

    /**
     * Get the variation records
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getVariationRecords()
    {
        return $this->variationRecords;
    }

    /**
     * Add a variation records
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $variationRecords
     * @return ConditionUndertaking
     */
    public function addVariationRecords($variationRecords)
    {
        if ($variationRecords instanceof ArrayCollection) {
            $this->variationRecords = new ArrayCollection(
                array_merge(
                    $this->variationRecords->toArray(),
                    $variationRecords->toArray()
                )
            );
        } elseif (!$this->variationRecords->contains($variationRecords)) {
            $this->variationRecords->add($variationRecords);
        }

        return $this;
    }

    /**
     * Remove a variation records
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $variationRecords
     * @return ConditionUndertaking
     */
    public function removeVariationRecords($variationRecords)
    {
        if ($this->variationRecords->contains($variationRecords)) {
            $this->variationRecords->removeElement($variationRecords);
        }

        return $this;
    }
}

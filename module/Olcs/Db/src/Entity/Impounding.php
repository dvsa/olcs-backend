<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Impounding Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="impounding",
 *    indexes={
 *        @ORM\Index(name="fk_impounding_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_impounding_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_impounding_transport_commissioner1_idx", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="fk_impounding_ref_data1_idx", columns={"outcome"}),
 *        @ORM\Index(name="fk_impounding_ref_data2_idx", columns={"impounding_type"}),
 *        @ORM\Index(name="fk_impounding_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_impounding_pi_venue1_idx", columns={"pi_venue_id"})
 *    }
 * )
 */
class Impounding implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\PresidingTcManyToOne,
        Traits\PiVenueManyToOne,
        Traits\OutcomeManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\HearingDateField,
        Traits\Notes4000Field,
        Traits\PiVenueOther255Field,
        Traits\Vrm20Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Impounding type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="impounding_type", referencedColumnName="id", nullable=false)
     */
    protected $impoundingType;

    /**
     * Impounding legislation type
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\RefData", inversedBy="impoundings", fetch="LAZY")
     * @ORM\JoinTable(name="impounding_legislation_type",
     *     joinColumns={
     *         @ORM\JoinColumn(name="impounding_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="impounding_legislation_type_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $impoundingLegislationTypes;

    /**
     * Application receipt date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="application_receipt_date", nullable=true)
     */
    protected $applicationReceiptDate;

    /**
     * Outcome sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="outcome_sent_date", nullable=true)
     */
    protected $outcomeSentDate;

    /**
     * Close date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="close_date", nullable=true)
     */
    protected $closeDate;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->impoundingLegislationTypes = new ArrayCollection();
    }

    /**
     * Set the impounding type
     *
     * @param \Olcs\Db\Entity\RefData $impoundingType
     * @return Impounding
     */
    public function setImpoundingType($impoundingType)
    {
        $this->impoundingType = $impoundingType;

        return $this;
    }

    /**
     * Get the impounding type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getImpoundingType()
    {
        return $this->impoundingType;
    }

    /**
     * Set the impounding legislation type
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundingLegislationTypes
     * @return Impounding
     */
    public function setImpoundingLegislationTypes($impoundingLegislationTypes)
    {
        $this->impoundingLegislationTypes = $impoundingLegislationTypes;

        return $this;
    }

    /**
     * Get the impounding legislation types
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getImpoundingLegislationTypes()
    {
        return $this->impoundingLegislationTypes;
    }

    /**
     * Add a impounding legislation types
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundingLegislationTypes
     * @return Impounding
     */
    public function addImpoundingLegislationTypes($impoundingLegislationTypes)
    {
        if ($impoundingLegislationTypes instanceof ArrayCollection) {
            $this->impoundingLegislationTypes = new ArrayCollection(
                array_merge(
                    $this->impoundingLegislationTypes->toArray(),
                    $impoundingLegislationTypes->toArray()
                )
            );
        } elseif (!$this->impoundingLegislationTypes->contains($impoundingLegislationTypes)) {
            $this->impoundingLegislationTypes->add($impoundingLegislationTypes);
        }

        return $this;
    }

    /**
     * Remove a impounding legislation types
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundingLegislationTypes
     * @return Impounding
     */
    public function removeImpoundingLegislationTypes($impoundingLegislationTypes)
    {
        if ($this->impoundingLegislationTypes->contains($impoundingLegislationTypes)) {
            $this->impoundingLegislationTypes->removeElement($impoundingLegislationTypes);
        }

        return $this;
    }

    /**
     * Set the application receipt date
     *
     * @param \DateTime $applicationReceiptDate
     * @return Impounding
     */
    public function setApplicationReceiptDate($applicationReceiptDate)
    {
        $this->applicationReceiptDate = $applicationReceiptDate;

        return $this;
    }

    /**
     * Get the application receipt date
     *
     * @return \DateTime
     */
    public function getApplicationReceiptDate()
    {
        return $this->applicationReceiptDate;
    }

    /**
     * Set the outcome sent date
     *
     * @param \DateTime $outcomeSentDate
     * @return Impounding
     */
    public function setOutcomeSentDate($outcomeSentDate)
    {
        $this->outcomeSentDate = $outcomeSentDate;

        return $this;
    }

    /**
     * Get the outcome sent date
     *
     * @return \DateTime
     */
    public function getOutcomeSentDate()
    {
        return $this->outcomeSentDate;
    }

    /**
     * Set the close date
     *
     * @param \DateTime $closeDate
     * @return Impounding
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    /**
     * Get the close date
     *
     * @return \DateTime
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }
}

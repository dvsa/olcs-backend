<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TmCaseDecision Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="tm_case_decision",
 *    indexes={
 *        @ORM\Index(name="fk_tm_case_decision_ref_data1_idx", columns={"decision"}),
 *        @ORM\Index(name="fk_tm_case_decision_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_tm_case_decision_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_tm_case_decision_cases1_idx", columns={"case_id"})
 *    }
 * )
 */
class TmCaseDecision implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CaseManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DecisionDateField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Decision
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="decision", referencedColumnName="id", nullable=false)
     */
    protected $decision;

    /**
     * Is msi
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_msi", nullable=false)
     */
    protected $isMsi = 0;

    /**
     * Notified date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="notified_date", nullable=true)
     */
    protected $notifiedDate;

    /**
     * Rehab measure
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\RefData", inversedBy="tmCaseDecisionRehabs")
     * @ORM\JoinTable(name="tm_case_decision_rehab",
     *     joinColumns={
     *         @ORM\JoinColumn(name="tm_case_decision_rehab_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="rehab_measure_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $rehabMeasures;

    /**
     * Repute not lost reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="repute_not_lost_reason", length=4000, nullable=true)
     */
    protected $reputeNotLostReason;

    /**
     * Unfitness end date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="unfitness_end_date", nullable=true)
     */
    protected $unfitnessEndDate;

    /**
     * Unfitness reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\RefData", mappedBy="tmCaseDecisionUnfitnesss")
     */
    protected $unfitnessReasons;

    /**
     * Unfitness start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="unfitness_start_date", nullable=true)
     */
    protected $unfitnessStartDate;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->unfitnessReasons = new ArrayCollection();
        $this->rehabMeasures = new ArrayCollection();
    }

    /**
     * Set the decision
     *
     * @param \Olcs\Db\Entity\RefData $decision
     * @return TmCaseDecision
     */
    public function setDecision($decision)
    {
        $this->decision = $decision;

        return $this;
    }

    /**
     * Get the decision
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * Set the is msi
     *
     * @param string $isMsi
     * @return TmCaseDecision
     */
    public function setIsMsi($isMsi)
    {
        $this->isMsi = $isMsi;

        return $this;
    }

    /**
     * Get the is msi
     *
     * @return string
     */
    public function getIsMsi()
    {
        return $this->isMsi;
    }

    /**
     * Set the notified date
     *
     * @param \DateTime $notifiedDate
     * @return TmCaseDecision
     */
    public function setNotifiedDate($notifiedDate)
    {
        $this->notifiedDate = $notifiedDate;

        return $this;
    }

    /**
     * Get the notified date
     *
     * @return \DateTime
     */
    public function getNotifiedDate()
    {
        return $this->notifiedDate;
    }

    /**
     * Set the rehab measure
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $rehabMeasures
     * @return TmCaseDecision
     */
    public function setRehabMeasures($rehabMeasures)
    {
        $this->rehabMeasures = $rehabMeasures;

        return $this;
    }

    /**
     * Get the rehab measures
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRehabMeasures()
    {
        return $this->rehabMeasures;
    }

    /**
     * Add a rehab measures
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $rehabMeasures
     * @return TmCaseDecision
     */
    public function addRehabMeasures($rehabMeasures)
    {
        if ($rehabMeasures instanceof ArrayCollection) {
            $this->rehabMeasures = new ArrayCollection(
                array_merge(
                    $this->rehabMeasures->toArray(),
                    $rehabMeasures->toArray()
                )
            );
        } elseif (!$this->rehabMeasures->contains($rehabMeasures)) {
            $this->rehabMeasures->add($rehabMeasures);
        }

        return $this;
    }

    /**
     * Remove a rehab measures
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $rehabMeasures
     * @return TmCaseDecision
     */
    public function removeRehabMeasures($rehabMeasures)
    {
        if ($this->rehabMeasures->contains($rehabMeasures)) {
            $this->rehabMeasures->removeElement($rehabMeasures);
        }

        return $this;
    }

    /**
     * Set the repute not lost reason
     *
     * @param string $reputeNotLostReason
     * @return TmCaseDecision
     */
    public function setReputeNotLostReason($reputeNotLostReason)
    {
        $this->reputeNotLostReason = $reputeNotLostReason;

        return $this;
    }

    /**
     * Get the repute not lost reason
     *
     * @return string
     */
    public function getReputeNotLostReason()
    {
        return $this->reputeNotLostReason;
    }

    /**
     * Set the unfitness end date
     *
     * @param \DateTime $unfitnessEndDate
     * @return TmCaseDecision
     */
    public function setUnfitnessEndDate($unfitnessEndDate)
    {
        $this->unfitnessEndDate = $unfitnessEndDate;

        return $this;
    }

    /**
     * Get the unfitness end date
     *
     * @return \DateTime
     */
    public function getUnfitnessEndDate()
    {
        return $this->unfitnessEndDate;
    }

    /**
     * Set the unfitness reason
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $unfitnessReasons
     * @return TmCaseDecision
     */
    public function setUnfitnessReasons($unfitnessReasons)
    {
        $this->unfitnessReasons = $unfitnessReasons;

        return $this;
    }

    /**
     * Get the unfitness reasons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getUnfitnessReasons()
    {
        return $this->unfitnessReasons;
    }

    /**
     * Add a unfitness reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $unfitnessReasons
     * @return TmCaseDecision
     */
    public function addUnfitnessReasons($unfitnessReasons)
    {
        if ($unfitnessReasons instanceof ArrayCollection) {
            $this->unfitnessReasons = new ArrayCollection(
                array_merge(
                    $this->unfitnessReasons->toArray(),
                    $unfitnessReasons->toArray()
                )
            );
        } elseif (!$this->unfitnessReasons->contains($unfitnessReasons)) {
            $this->unfitnessReasons->add($unfitnessReasons);
        }

        return $this;
    }

    /**
     * Remove a unfitness reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $unfitnessReasons
     * @return TmCaseDecision
     */
    public function removeUnfitnessReasons($unfitnessReasons)
    {
        if ($this->unfitnessReasons->contains($unfitnessReasons)) {
            $this->unfitnessReasons->removeElement($unfitnessReasons);
        }

        return $this;
    }

    /**
     * Set the unfitness start date
     *
     * @param \DateTime $unfitnessStartDate
     * @return TmCaseDecision
     */
    public function setUnfitnessStartDate($unfitnessStartDate)
    {
        $this->unfitnessStartDate = $unfitnessStartDate;

        return $this;
    }

    /**
     * Get the unfitness start date
     *
     * @return \DateTime
     */
    public function getUnfitnessStartDate()
    {
        return $this->unfitnessStartDate;
    }
}

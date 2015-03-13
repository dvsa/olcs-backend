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
 *        @ORM\Index(name="ix_tm_case_decision_decision", columns={"decision"}),
 *        @ORM\Index(name="ix_tm_case_decision_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_tm_case_decision_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_tm_case_decision_case_id", columns={"case_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_tm_case_decision_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class TmCaseDecision implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DecisionDateField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", inversedBy="tmDecisions")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

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
     * @ORM\Column(type="yesno", name="is_msi", nullable=false, options={"default": 0})
     */
    protected $isMsi = 0;

    /**
     * No further action reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="no_further_action_reason", length=4000, nullable=true)
     */
    protected $noFurtherActionReason;

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
     * @ORM\Column(type="string", name="repute_not_lost_reason", length=500, nullable=true)
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
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\RefData", inversedBy="tmCaseDecisionUnfitnesss")
     * @ORM\JoinTable(name="tm_case_decision_unfitness",
     *     joinColumns={
     *         @ORM\JoinColumn(name="tm_case_decision_unfitness_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="unfitness_reason_id", referencedColumnName="id")
     *     }
     * )
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
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return TmCaseDecision
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
     * Set the no further action reason
     *
     * @param string $noFurtherActionReason
     * @return TmCaseDecision
     */
    public function setNoFurtherActionReason($noFurtherActionReason)
    {
        $this->noFurtherActionReason = $noFurtherActionReason;

        return $this;
    }

    /**
     * Get the no further action reason
     *
     * @return string
     */
    public function getNoFurtherActionReason()
    {
        return $this->noFurtherActionReason;
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

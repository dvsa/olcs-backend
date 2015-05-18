<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TmCaseDecision Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
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
abstract class AbstractTmCaseDecision
{

    /**
     * Case
     *
     * @var \Dvsa\Olcs\Api\Entity\Cases\Cases
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Cases\Cases", fetch="LAZY", inversedBy="tmDecisions")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Decision
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="decision", referencedColumnName="id", nullable=false)
     */
    protected $decision;

    /**
     * Decision date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="decision_date", nullable=true)
     */
    protected $decisionDate;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Is msi
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_msi", nullable=false, options={"default": 0})
     */
    protected $isMsi = 0;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

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
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Rehab measure
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", inversedBy="tmCaseDecisions", fetch="LAZY")
     * @ORM\JoinTable(name="tm_case_decision_rehab",
     *     joinColumns={
     *         @ORM\JoinColumn(name="tm_case_decision_id", referencedColumnName="id")
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
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", inversedBy="tmCaseDecisions", fetch="LAZY")
     * @ORM\JoinTable(name="tm_case_decision_unfitness",
     *     joinColumns={
     *         @ORM\JoinColumn(name="tm_case_decision_id", referencedColumnName="id")
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
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->initCollections();
    }

    public function initCollections()
    {
        $this->unfitnessReasons = new ArrayCollection();
        $this->rehabMeasures = new ArrayCollection();
    }

    /**
     * Set the case
     *
     * @param \Dvsa\Olcs\Api\Entity\Cases\Cases $case
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
     * @return \Dvsa\Olcs\Api\Entity\Cases\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return TmCaseDecision
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return TmCaseDecision
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the decision
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $decision
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
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * Set the decision date
     *
     * @param \DateTime $decisionDate
     * @return TmCaseDecision
     */
    public function setDecisionDate($decisionDate)
    {
        $this->decisionDate = $decisionDate;

        return $this;
    }

    /**
     * Get the decision date
     *
     * @return \DateTime
     */
    public function getDecisionDate()
    {
        return $this->decisionDate;
    }

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return TmCaseDecision
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return TmCaseDecision
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return TmCaseDecision
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return TmCaseDecision
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
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
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return TmCaseDecision
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
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

    /**
     * Set the version
     *
     * @param int $version
     * @return TmCaseDecision
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}

<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
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
 *        @ORM\Index(name="fk_tm_case_decision_ref_data1_idx", 
 *            columns={"decision"}),
 *        @ORM\Index(name="fk_tm_case_decision_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_tm_case_decision_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_tm_case_decision_cases1_idx", 
 *            columns={"case_id"})
 *    }
 * )
 */
class TmCaseDecision implements Interfaces\EntityInterface
{

    /**
     * Decision
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="decision", referencedColumnName="id", nullable=false)
     */
    protected $decision;

    /**
     * Notified date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="notified_date", nullable=true)
     */
    protected $notifiedDate;

    /**
     * Is msi
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_msi", nullable=false)
     */
    protected $isMsi = 0;

    /**
     * Repute not lost reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="repute_not_lost_reason", length=4000, nullable=true)
     */
    protected $reputeNotLostReason;

    /**
     * Unfitness start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="unfitness_start_date", nullable=true)
     */
    protected $unfitnessStartDate;

    /**
     * Unfitness end date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="unfitness_end_date", nullable=true)
     */
    protected $unfitnessEndDate;

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
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", fetch="LAZY")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

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
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

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

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the last modified by
     *
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the decision date
     *
     * @param \DateTime $decisionDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * @return bool
     */
    public function isDeleted()
    {
        return !is_null($this->deletedDate);
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}

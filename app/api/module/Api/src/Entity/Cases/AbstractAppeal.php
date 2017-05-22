<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Appeal Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="appeal",
 *    indexes={
 *        @ORM\Index(name="ix_appeal_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_appeal_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_appeal_reason", columns={"reason"}),
 *        @ORM\Index(name="ix_appeal_outcome", columns={"outcome"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_appeal_case_id", columns={"case_id"}),
 *        @ORM\UniqueConstraint(name="uk_appeal_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
abstract class AbstractAppeal implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Appeal date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="appeal_date", nullable=true)
     */
    protected $appealDate;

    /**
     * Appeal no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="appeal_no", length=20, nullable=true)
     */
    protected $appealNo;

    /**
     * Case
     *
     * @var \Dvsa\Olcs\Api\Entity\Cases\Cases
     *
     * @ORM\OneToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Cases\Cases",
     *     fetch="LAZY",
     *     inversedBy="appeal"
     * )
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Comment
     *
     * @var string
     *
     * @ORM\Column(type="string", name="comment", length=1024, nullable=true)
     */
    protected $comment;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="create")
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
     * Deadline date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deadline_date", nullable=true)
     */
    protected $deadlineDate;

    /**
     * Decision date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="decision_date", nullable=true)
     */
    protected $decisionDate;

    /**
     * Dvsa notified
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="dvsa_notified", nullable=false, options={"default": 0})
     */
    protected $dvsaNotified = 0;

    /**
     * Hearing date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="hearing_date", nullable=true)
     */
    protected $hearingDate;

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
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="update")
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
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Olbs type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_type", length=32, nullable=true)
     */
    protected $olbsType;

    /**
     * Outcome
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="outcome", referencedColumnName="id", nullable=true)
     */
    protected $outcome;

    /**
     * Outline ground
     *
     * @var string
     *
     * @ORM\Column(type="string", name="outline_ground", length=1024, nullable=true)
     */
    protected $outlineGround;

    /**
     * Papers due date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="papers_due_date", nullable=true)
     */
    protected $papersDueDate;

    /**
     * Papers due tc date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="papers_due_tc_date", nullable=true)
     */
    protected $papersDueTcDate;

    /**
     * Papers sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="papers_sent_date", nullable=true)
     */
    protected $papersSentDate;

    /**
     * Papers sent tc date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="papers_sent_tc_date", nullable=true)
     */
    protected $papersSentTcDate;

    /**
     * Reason
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="reason", referencedColumnName="id", nullable=true)
     */
    protected $reason;

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
     * Withdrawn date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="withdrawn_date", nullable=true)
     */
    protected $withdrawnDate;

    /**
     * Set the appeal date
     *
     * @param \DateTime $appealDate new value being set
     *
     * @return Appeal
     */
    public function setAppealDate($appealDate)
    {
        $this->appealDate = $appealDate;

        return $this;
    }

    /**
     * Get the appeal date
     *
     * @return \DateTime
     */
    public function getAppealDate()
    {
        return $this->appealDate;
    }

    /**
     * Set the appeal no
     *
     * @param string $appealNo new value being set
     *
     * @return Appeal
     */
    public function setAppealNo($appealNo)
    {
        $this->appealNo = $appealNo;

        return $this;
    }

    /**
     * Get the appeal no
     *
     * @return string
     */
    public function getAppealNo()
    {
        return $this->appealNo;
    }

    /**
     * Set the case
     *
     * @param \Dvsa\Olcs\Api\Entity\Cases\Cases $case entity being set as the value
     *
     * @return Appeal
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
     * Set the comment
     *
     * @param string $comment new value being set
     *
     * @return Appeal
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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return Appeal
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
     * @param \DateTime $createdOn new value being set
     *
     * @return Appeal
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
     * Set the deadline date
     *
     * @param \DateTime $deadlineDate new value being set
     *
     * @return Appeal
     */
    public function setDeadlineDate($deadlineDate)
    {
        $this->deadlineDate = $deadlineDate;

        return $this;
    }

    /**
     * Get the deadline date
     *
     * @return \DateTime
     */
    public function getDeadlineDate()
    {
        return $this->deadlineDate;
    }

    /**
     * Set the decision date
     *
     * @param \DateTime $decisionDate new value being set
     *
     * @return Appeal
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
     * Set the dvsa notified
     *
     * @param string $dvsaNotified new value being set
     *
     * @return Appeal
     */
    public function setDvsaNotified($dvsaNotified)
    {
        $this->dvsaNotified = $dvsaNotified;

        return $this;
    }

    /**
     * Get the dvsa notified
     *
     * @return string
     */
    public function getDvsaNotified()
    {
        return $this->dvsaNotified;
    }

    /**
     * Set the hearing date
     *
     * @param \DateTime $hearingDate new value being set
     *
     * @return Appeal
     */
    public function setHearingDate($hearingDate)
    {
        $this->hearingDate = $hearingDate;

        return $this;
    }

    /**
     * Get the hearing date
     *
     * @return \DateTime
     */
    public function getHearingDate()
    {
        return $this->hearingDate;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return Appeal
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return Appeal
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
     * @param \DateTime $lastModifiedOn new value being set
     *
     * @return Appeal
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
     * Set the olbs key
     *
     * @param int $olbsKey new value being set
     *
     * @return Appeal
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
     * Set the olbs type
     *
     * @param string $olbsType new value being set
     *
     * @return Appeal
     */
    public function setOlbsType($olbsType)
    {
        $this->olbsType = $olbsType;

        return $this;
    }

    /**
     * Get the olbs type
     *
     * @return string
     */
    public function getOlbsType()
    {
        return $this->olbsType;
    }

    /**
     * Set the outcome
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $outcome entity being set as the value
     *
     * @return Appeal
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;

        return $this;
    }

    /**
     * Get the outcome
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getOutcome()
    {
        return $this->outcome;
    }

    /**
     * Set the outline ground
     *
     * @param string $outlineGround new value being set
     *
     * @return Appeal
     */
    public function setOutlineGround($outlineGround)
    {
        $this->outlineGround = $outlineGround;

        return $this;
    }

    /**
     * Get the outline ground
     *
     * @return string
     */
    public function getOutlineGround()
    {
        return $this->outlineGround;
    }

    /**
     * Set the papers due date
     *
     * @param \DateTime $papersDueDate new value being set
     *
     * @return Appeal
     */
    public function setPapersDueDate($papersDueDate)
    {
        $this->papersDueDate = $papersDueDate;

        return $this;
    }

    /**
     * Get the papers due date
     *
     * @return \DateTime
     */
    public function getPapersDueDate()
    {
        return $this->papersDueDate;
    }

    /**
     * Set the papers due tc date
     *
     * @param \DateTime $papersDueTcDate new value being set
     *
     * @return Appeal
     */
    public function setPapersDueTcDate($papersDueTcDate)
    {
        $this->papersDueTcDate = $papersDueTcDate;

        return $this;
    }

    /**
     * Get the papers due tc date
     *
     * @return \DateTime
     */
    public function getPapersDueTcDate()
    {
        return $this->papersDueTcDate;
    }

    /**
     * Set the papers sent date
     *
     * @param \DateTime $papersSentDate new value being set
     *
     * @return Appeal
     */
    public function setPapersSentDate($papersSentDate)
    {
        $this->papersSentDate = $papersSentDate;

        return $this;
    }

    /**
     * Get the papers sent date
     *
     * @return \DateTime
     */
    public function getPapersSentDate()
    {
        return $this->papersSentDate;
    }

    /**
     * Set the papers sent tc date
     *
     * @param \DateTime $papersSentTcDate new value being set
     *
     * @return Appeal
     */
    public function setPapersSentTcDate($papersSentTcDate)
    {
        $this->papersSentTcDate = $papersSentTcDate;

        return $this;
    }

    /**
     * Get the papers sent tc date
     *
     * @return \DateTime
     */
    public function getPapersSentTcDate()
    {
        return $this->papersSentTcDate;
    }

    /**
     * Set the reason
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $reason entity being set as the value
     *
     * @return Appeal
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the reason
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return Appeal
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
     * Set the withdrawn date
     *
     * @param \DateTime $withdrawnDate new value being set
     *
     * @return Appeal
     */
    public function setWithdrawnDate($withdrawnDate)
    {
        $this->withdrawnDate = $withdrawnDate;

        return $this;
    }

    /**
     * Get the withdrawn date
     *
     * @return \DateTime
     */
    public function getWithdrawnDate()
    {
        return $this->withdrawnDate;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     *
     * @return void
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                $this->$property = null;
            }
        }
    }
}

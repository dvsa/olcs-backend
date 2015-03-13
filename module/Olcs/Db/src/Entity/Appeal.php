<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Appeal Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="appeal",
 *    indexes={
 *        @ORM\Index(name="ix_appeal_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_appeal_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_appeal_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_appeal_reason", columns={"reason"}),
 *        @ORM\Index(name="ix_appeal_outcome", columns={"outcome"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_appeal_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Appeal implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DecisionDateField,
        Traits\HearingDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\OlbsType32Field,
        Traits\OutcomeManyToOne,
        Traits\CustomVersionField,
        Traits\WithdrawnDateField;

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
     * @ORM\Column(type="string", name="appeal_no", length=20, nullable=false)
     */
    protected $appealNo;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", inversedBy="appeals")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=true)
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
     * Deadline date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deadline_date", nullable=true)
     */
    protected $deadlineDate;

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
     * Papers sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="papers_sent_date", nullable=true)
     */
    protected $papersSentDate;

    /**
     * Reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="reason", referencedColumnName="id", nullable=true)
     */
    protected $reason;

    /**
     * Set the appeal date
     *
     * @param \DateTime $appealDate
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
     * @param string $appealNo
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
     * @param \Olcs\Db\Entity\Cases $case
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
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the comment
     *
     * @param string $comment
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
     * Set the deadline date
     *
     * @param \DateTime $deadlineDate
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
     * Set the outline ground
     *
     * @param string $outlineGround
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
     * @param \DateTime $papersDueDate
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
     * Set the papers sent date
     *
     * @param \DateTime $papersSentDate
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
     * Set the reason
     *
     * @param \Olcs\Db\Entity\RefData $reason
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getReason()
    {
        return $this->reason;
    }
}

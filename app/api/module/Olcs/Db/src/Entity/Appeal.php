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
 *        @ORM\Index(name="fk_appeal_case1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_appeal_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_appeal_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_appeal_ref_data1_idx", columns={"reason"}),
 *        @ORM\Index(name="fk_appeal_ref_data2_idx", columns={"outcome"})
 *    }
 * )
 */
class Appeal implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\OutcomeManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\HearingDateField,
        Traits\DecisionDateField,
        Traits\WithdrawnDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="reason", referencedColumnName="id", nullable=true)
     */
    protected $reason;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", fetch="LAZY", inversedBy="appeals")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=true)
     */
    protected $case;

    /**
     * Appeal no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="appeal_no", length=20, nullable=true)
     */
    protected $appealNo;

    /**
     * Tm case id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tm_case_id", nullable=true)
     */
    protected $tmCaseId;

    /**
     * Deadline date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deadline_date", nullable=true)
     */
    protected $deadlineDate;

    /**
     * Appeal date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="appeal_date", nullable=true)
     */
    protected $appealDate;

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
     * Comment
     *
     * @var string
     *
     * @ORM\Column(type="string", name="comment", length=1024, nullable=true)
     */
    protected $comment;

    /**
     * Papers sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="papers_sent_date", nullable=true)
     */
    protected $papersSentDate;

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
     * Set the tm case id
     *
     * @param int $tmCaseId
     * @return Appeal
     */
    public function setTmCaseId($tmCaseId)
    {
        $this->tmCaseId = $tmCaseId;

        return $this;
    }

    /**
     * Get the tm case id
     *
     * @return int
     */
    public function getTmCaseId()
    {
        return $this->tmCaseId;
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
}

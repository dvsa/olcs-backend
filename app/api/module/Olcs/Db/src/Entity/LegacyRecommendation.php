<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * LegacyRecommendation Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="legacy_recommendation",
 *    indexes={
 *        @ORM\Index(name="ix_legacy_recommendation_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_legacy_recommendation_from_user_id", columns={"from_user_id"}),
 *        @ORM\Index(name="ix_legacy_recommendation_to_user_id", columns={"to_user_id"}),
 *        @ORM\Index(name="ix_legacy_recommendation_action_id", columns={"action_id"}),
 *        @ORM\Index(name="ix_legacy_recommendation_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_legacy_recommendation_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class LegacyRecommendation implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CaseManyToOneAlt1,
        Traits\Comment4000Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\EffectiveDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Action
     *
     * @var \Olcs\Db\Entity\LegacyCaseAction
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\LegacyCaseAction")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id", nullable=false)
     */
    protected $action;

    /**
     * From user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id", nullable=true)
     */
    protected $fromUser;

    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="text", name="notes", length=65535, nullable=true)
     */
    protected $notes;

    /**
     * Pi decision
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pi_decision", length=255, nullable=true)
     */
    protected $piDecision;

    /**
     * Pi reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pi_reason", length=255, nullable=true)
     */
    protected $piReason;

    /**
     * Rec date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="rec_date", nullable=false)
     */
    protected $recDate;

    /**
     * Request
     *
     * @var string
     *
     * @ORM\Column(type="string", name="request", length=20, nullable=true)
     */
    protected $request;

    /**
     * Revoke lic
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="revoke_lic", nullable=true)
     */
    protected $revokeLic;

    /**
     * Status
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="status", nullable=true)
     */
    protected $status;

    /**
     * To user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id", nullable=true)
     */
    protected $toUser;

    /**
     * Total points
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="total_points", nullable=true)
     */
    protected $totalPoints;

    /**
     * Set the action
     *
     * @param \Olcs\Db\Entity\LegacyCaseAction $action
     * @return LegacyRecommendation
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get the action
     *
     * @return \Olcs\Db\Entity\LegacyCaseAction
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the from user
     *
     * @param \Olcs\Db\Entity\User $fromUser
     * @return LegacyRecommendation
     */
    public function setFromUser($fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get the from user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set the notes
     *
     * @param string $notes
     * @return LegacyRecommendation
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
     * Set the pi decision
     *
     * @param string $piDecision
     * @return LegacyRecommendation
     */
    public function setPiDecision($piDecision)
    {
        $this->piDecision = $piDecision;

        return $this;
    }

    /**
     * Get the pi decision
     *
     * @return string
     */
    public function getPiDecision()
    {
        return $this->piDecision;
    }

    /**
     * Set the pi reason
     *
     * @param string $piReason
     * @return LegacyRecommendation
     */
    public function setPiReason($piReason)
    {
        $this->piReason = $piReason;

        return $this;
    }

    /**
     * Get the pi reason
     *
     * @return string
     */
    public function getPiReason()
    {
        return $this->piReason;
    }

    /**
     * Set the rec date
     *
     * @param \DateTime $recDate
     * @return LegacyRecommendation
     */
    public function setRecDate($recDate)
    {
        $this->recDate = $recDate;

        return $this;
    }

    /**
     * Get the rec date
     *
     * @return \DateTime
     */
    public function getRecDate()
    {
        return $this->recDate;
    }

    /**
     * Set the request
     *
     * @param string $request
     * @return LegacyRecommendation
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the request
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the revoke lic
     *
     * @param string $revokeLic
     * @return LegacyRecommendation
     */
    public function setRevokeLic($revokeLic)
    {
        $this->revokeLic = $revokeLic;

        return $this;
    }

    /**
     * Get the revoke lic
     *
     * @return string
     */
    public function getRevokeLic()
    {
        return $this->revokeLic;
    }

    /**
     * Set the status
     *
     * @param string $status
     * @return LegacyRecommendation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the to user
     *
     * @param \Olcs\Db\Entity\User $toUser
     * @return LegacyRecommendation
     */
    public function setToUser($toUser)
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get the to user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * Set the total points
     *
     * @param int $totalPoints
     * @return LegacyRecommendation
     */
    public function setTotalPoints($totalPoints)
    {
        $this->totalPoints = $totalPoints;

        return $this;
    }

    /**
     * Get the total points
     *
     * @return int
     */
    public function getTotalPoints()
    {
        return $this->totalPoints;
    }
}

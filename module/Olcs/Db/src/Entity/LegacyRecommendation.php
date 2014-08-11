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
 *        @ORM\Index(name="fk_case_recommendation_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_case_recommendation_user1_idx", columns={"from_user_id"}),
 *        @ORM\Index(name="fk_case_recommendation_user2_idx", columns={"to_user_id"}),
 *        @ORM\Index(name="fk_legacy_recommendation_legacy_case_action1_idx", columns={"action_id"}),
 *        @ORM\Index(name="fk_legacy_recommendation_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_legacy_recommendation_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class LegacyRecommendation implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CaseManyToOne,
        Traits\Comment4000Field,
        Traits\EffectiveDateField,
        Traits\NotesField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Action
     *
     * @var \Olcs\Db\Entity\LegacyCaseAction
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\LegacyCaseAction")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     */
    protected $action;

    /**
     * To user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
     */
    protected $toUser;

    /**
     * From user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id")
     */
    protected $fromUser;

    /**
     * Rec date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="rec_date", nullable=false)
     */
    protected $recDate;

    /**
     * Pi reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pi_reason", length=255, nullable=true)
     */
    protected $piReason;

    /**
     * Pi decision
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pi_decision", length=255, nullable=true)
     */
    protected $piDecision;

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
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="revoke_lic", nullable=true)
     */
    protected $revokeLic;

    /**
     * Status
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="status", nullable=true)
     */
    protected $status;

    /**
     * Total points
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="total_points", nullable=true)
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
     * @param unknown $revokeLic
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
     * @return unknown
     */
    public function getRevokeLic()
    {
        return $this->revokeLic;
    }


    /**
     * Set the status
     *
     * @param unknown $status
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
     * @return unknown
     */
    public function getStatus()
    {
        return $this->status;
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

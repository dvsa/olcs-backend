<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * ProposeToRevoke Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="propose_to_revoke",
 *    indexes={
 *        @ORM\Index(name="fk_propose_to_revoke_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_propose_to_revoke_presiding_tc1_idx", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="fk_propose_to_revoke_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_propose_to_revoke_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class ProposeToRevoke implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CaseManyToOne,
        Traits\CreatedByManyToOne,
        Traits\PresidingTcManyToOne,
        Traits\ClosedDateField,
        Traits\Comment4000Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Pi reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\PiReason", inversedBy="proposeToRevokes")
     * @ORM\JoinTable(name="ptr_pi_reason",
     *     joinColumns={
     *         @ORM\JoinColumn(name="propose_to_revoke_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="pi_reason_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $piReasons;

    /**
     * Ptr agreed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="ptr_agreed_date", nullable=true)
     */
    protected $ptrAgreedDate;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->piReasons = new ArrayCollection();
    }

    /**
     * Set the pi reason
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $piReasons

     * @return \Olcs\Db\Entity\ProposeToRevoke
     */
    public function setPiReasons($piReasons)
    {
        $this->piReasons = $piReasons;

        return $this;
    }

    /**
     * Get the pi reason
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getPiReasons()
    {
        return $this->piReasons;
    }

    /**
     * Set the ptr agreed date
     *
     * @param \DateTime $ptrAgreedDate
     * @return \Olcs\Db\Entity\ProposeToRevoke
     */
    public function setPtrAgreedDate($ptrAgreedDate)
    {
        $this->ptrAgreedDate = $ptrAgreedDate;

        return $this;
    }

    /**
     * Get the ptr agreed date
     *
     * @return \DateTime
     */
    public function getPtrAgreedDate()
    {
        return $this->ptrAgreedDate;
    }
}

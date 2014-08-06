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
     * Reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Reason", inversedBy="proposeToRevokes")
     * @ORM\JoinTable(name="ptr_reason",
     *     joinColumns={
     *         @ORM\JoinColumn(name="propose_to_revoke_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="reason_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $reasons;

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
        $this->reasons = new ArrayCollection();
    }

    /**
     * Set the reason
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons

     * @return \Olcs\Db\Entity\ProposeToRevoke
     */
    public function setReasons($reasons)
    {
        $this->reasons = $reasons;

        return $this;
    }

    /**
     * Get the reason
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getReasons()
    {
        return $this->reasons;
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

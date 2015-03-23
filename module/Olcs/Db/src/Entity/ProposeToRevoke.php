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
 *        @ORM\Index(name="ix_propose_to_revoke_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_propose_to_revoke_presiding_tc_id", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="ix_propose_to_revoke_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_propose_to_revoke_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class ProposeToRevoke implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CaseManyToOneAlt1,
        Traits\ClosedDateField,
        Traits\Comment4000Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Presiding tc
     *
     * @var \Olcs\Db\Entity\PresidingTc
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PresidingTc")
     * @ORM\JoinColumn(name="presiding_tc_id", referencedColumnName="id", nullable=false)
     */
    protected $presidingTc;

    /**
     * Ptr agreed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="ptr_agreed_date", nullable=true)
     */
    protected $ptrAgreedDate;

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
     * Initialise the collections
     */
    public function __construct()
    {
        $this->reasons = new ArrayCollection();
    }

    /**
     * Set the presiding tc
     *
     * @param \Olcs\Db\Entity\PresidingTc $presidingTc
     * @return ProposeToRevoke
     */
    public function setPresidingTc($presidingTc)
    {
        $this->presidingTc = $presidingTc;

        return $this;
    }

    /**
     * Get the presiding tc
     *
     * @return \Olcs\Db\Entity\PresidingTc
     */
    public function getPresidingTc()
    {
        return $this->presidingTc;
    }

    /**
     * Set the ptr agreed date
     *
     * @param \DateTime $ptrAgreedDate
     * @return ProposeToRevoke
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

    /**
     * Set the reason
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons
     * @return ProposeToRevoke
     */
    public function setReasons($reasons)
    {
        $this->reasons = $reasons;

        return $this;
    }

    /**
     * Get the reasons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getReasons()
    {
        return $this->reasons;
    }

    /**
     * Add a reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons
     * @return ProposeToRevoke
     */
    public function addReasons($reasons)
    {
        if ($reasons instanceof ArrayCollection) {
            $this->reasons = new ArrayCollection(
                array_merge(
                    $this->reasons->toArray(),
                    $reasons->toArray()
                )
            );
        } elseif (!$this->reasons->contains($reasons)) {
            $this->reasons->add($reasons);
        }

        return $this;
    }

    /**
     * Remove a reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons
     * @return ProposeToRevoke
     */
    public function removeReasons($reasons)
    {
        if ($this->reasons->contains($reasons)) {
            $this->reasons->removeElement($reasons);
        }

        return $this;
    }
}

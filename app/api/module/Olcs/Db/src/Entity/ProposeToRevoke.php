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
        Traits\CaseManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\PresidingTcManyToOneAlt1,
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
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Reason", inversedBy="proposeToRevokes", fetch="LAZY")
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
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
}

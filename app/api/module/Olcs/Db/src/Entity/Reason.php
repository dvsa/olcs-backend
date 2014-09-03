<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Reason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="reason",
 *    indexes={
 *        @ORM\Index(name="fk_pi_reason_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_reason_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_reason_ref_data1_idx", columns={"goods_or_psv"})
 *    }
 * )
 */
class Reason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\GoodsOrPsvManyToOneAlt1,
        Traits\LastModifiedByManyToOne,
        Traits\SectionCode50Field,
        Traits\Description255FieldAlt1,
        Traits\IsNiField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Propose to revoke
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\ProposeToRevoke", mappedBy="reasons", fetch="LAZY")
     */
    protected $proposeToRevokes;

    /**
     * Pi
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Pi", mappedBy="reasons", fetch="LAZY")
     */
    protected $pis;

    /**
     * Is read only
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_read_only", nullable=false)
     */
    protected $isReadOnly;

    /**
     * Is propose to revoke
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_propose_to_revoke", nullable=false)
     */
    protected $isProposeToRevoke;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->proposeToRevokes = new ArrayCollection();
        $this->pis = new ArrayCollection();
    }

    /**
     * Set the propose to revoke
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $proposeToRevokes
     * @return Reason
     */
    public function setProposeToRevokes($proposeToRevokes)
    {
        $this->proposeToRevokes = $proposeToRevokes;

        return $this;
    }

    /**
     * Get the propose to revokes
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProposeToRevokes()
    {
        return $this->proposeToRevokes;
    }

    /**
     * Set the pi
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return Reason
     */
    public function setPis($pis)
    {
        $this->pis = $pis;

        return $this;
    }

    /**
     * Get the pis
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPis()
    {
        return $this->pis;
    }

    /**
     * Set the is read only
     *
     * @param string $isReadOnly
     * @return Reason
     */
    public function setIsReadOnly($isReadOnly)
    {
        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    /**
     * Get the is read only
     *
     * @return string
     */
    public function getIsReadOnly()
    {
        return $this->isReadOnly;
    }

    /**
     * Set the is propose to revoke
     *
     * @param string $isProposeToRevoke
     * @return Reason
     */
    public function setIsProposeToRevoke($isProposeToRevoke)
    {
        $this->isProposeToRevoke = $isProposeToRevoke;

        return $this;
    }

    /**
     * Get the is propose to revoke
     *
     * @return string
     */
    public function getIsProposeToRevoke()
    {
        return $this->isProposeToRevoke;
    }
}

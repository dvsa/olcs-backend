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
 *        @ORM\Index(name="fk_reason_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_reason_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Reason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\GoodsOrPsv3Field,
        Traits\IsDecisionField,
        Traits\Description255Field,
        Traits\IsNiField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Propose to revoke
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\ProposeToRevoke", mappedBy="reasons")
     */
    protected $proposeToRevokes;

    /**
     * Section code
     *
     * @var string
     *
     * @ORM\Column(type="string", name="section_code", length=50, nullable=false)
     */
    protected $sectionCode;

    /**
     * Is read only
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_read_only", nullable=false)
     */
    protected $isReadOnly;

    /**
     * Is propose to revoke
     *
     * @var unknown
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
    }

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
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
     * Add a propose to revokes
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $proposeToRevokes
     * @return Reason
     */
    public function addProposeToRevokes($proposeToRevokes)
    {
        if ($proposeToRevokes instanceof ArrayCollection) {
            $this->proposeToRevokes = new ArrayCollection(
                array_merge(
                    $this->proposeToRevokes->toArray(),
                    $proposeToRevokes->toArray()
                )
            );
        } elseif (!$this->proposeToRevokes->contains($proposeToRevokes)) {
            $this->proposeToRevokes->add($proposeToRevokes);
        }

        return $this;
    }

    /**
     * Remove a propose to revokes
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $proposeToRevokes
     * @return Reason
     */
    public function removeProposeToRevokes($proposeToRevokes)
    {
        if ($this->proposeToRevokes->contains($proposeToRevokes)) {
            $this->proposeToRevokes->remove($proposeToRevokes);
        }

        return $this;
    }


    /**
     * Set the section code
     *
     * @param string $sectionCode
     * @return Reason
     */
    public function setSectionCode($sectionCode)
    {
        $this->sectionCode = $sectionCode;

        return $this;
    }

    /**
     * Get the section code
     *
     * @return string
     */
    public function getSectionCode()
    {
        return $this->sectionCode;
    }


    /**
     * Set the is read only
     *
     * @param unknown $isReadOnly
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
     * @return unknown
     */
    public function getIsReadOnly()
    {
        return $this->isReadOnly;
    }


    /**
     * Set the is propose to revoke
     *
     * @param unknown $isProposeToRevoke
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
     * @return unknown
     */
    public function getIsProposeToRevoke()
    {
        return $this->isProposeToRevoke;
    }

}

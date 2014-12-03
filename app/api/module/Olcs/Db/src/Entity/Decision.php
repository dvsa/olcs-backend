<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Decision Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="decision",
 *    indexes={
 *        @ORM\Index(name="fk_decision_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_decision_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_decision_ref_data1_idx", columns={"goods_or_psv"})
 *    }
 * )
 */
class Decision implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\GoodsOrPsvManyToOneAlt1,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\SectionCode50Field,
        Traits\Description255Field,
        Traits\IsReadOnlyField,
        Traits\IsNiField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Pi
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Pi", mappedBy="decisions")
     */
    protected $pis;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->pis = new ArrayCollection();
    }

    /**
     * Set the pi
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return Decision
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
     * Add a pis
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return Decision
     */
    public function addPis($pis)
    {
        if ($pis instanceof ArrayCollection) {
            $this->pis = new ArrayCollection(
                array_merge(
                    $this->pis->toArray(),
                    $pis->toArray()
                )
            );
        } elseif (!$this->pis->contains($pis)) {
            $this->pis->add($pis);
        }

        return $this;
    }

    /**
     * Remove a pis
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return Decision
     */
    public function removePis($pis)
    {
        if ($this->pis->contains($pis)) {
            $this->pis->removeElement($pis);
        }

        return $this;
    }
}

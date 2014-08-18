<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * RefData Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ref_data",
 *    indexes={
 *        @ORM\Index(name="fk_ref_data_ref_data_category1_idx", columns={"ref_data_category_id"})
 *    }
 * )
 */
class RefData implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Id32Identity,
        Traits\Description100Field;

    /**
     * Ref data category
     *
     * @var \Olcs\Db\Entity\RefDataCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefDataCategory", fetch="LAZY")
     * @ORM\JoinColumn(name="ref_data_category_id", referencedColumnName="id", nullable=false)
     */
    protected $refDataCategory;

    /**
     * Impounding
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Impounding", mappedBy="impoundingLegislationTypes", fetch="LAZY")
     */
    protected $impoundings;

    /**
     * Olbs key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_key", length=20, nullable=true)
     */
    protected $olbsKey;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->impoundings = new ArrayCollection();
    }


    /**
     * Set the ref data category
     *
     * @param \Olcs\Db\Entity\RefDataCategory $refDataCategory
     * @return RefData
     */
    public function setRefDataCategory($refDataCategory)
    {
        $this->refDataCategory = $refDataCategory;

        return $this;
    }

    /**
     * Get the ref data category
     *
     * @return \Olcs\Db\Entity\RefDataCategory
     */
    public function getRefDataCategory()
    {
        return $this->refDataCategory;
    }

    /**
     * Set the impounding
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundings
     * @return RefData
     */
    public function setImpoundings($impoundings)
    {
        $this->impoundings = $impoundings;

        return $this;
    }

    /**
     * Get the impoundings
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getImpoundings()
    {
        return $this->impoundings;
    }

    /**
     * Add a impoundings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundings
     * @return RefData
     */
    public function addImpoundings($impoundings)
    {
        if ($impoundings instanceof ArrayCollection) {
            $this->impoundings = new ArrayCollection(
                array_merge(
                    $this->impoundings->toArray(),
                    $impoundings->toArray()
                )
            );
        } elseif (!$this->impoundings->contains($impoundings)) {
            $this->impoundings->add($impoundings);
        }

        return $this;
    }

    /**
     * Remove a impoundings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundings
     * @return RefData
     */
    public function removeImpoundings($impoundings)
    {
        if ($this->impoundings->contains($impoundings)) {
            $this->impoundings->remove($impoundings);
        }

        return $this;
    }

    /**
     * Set the olbs key
     *
     * @param string $olbsKey
     * @return RefData
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return string
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
    }
}

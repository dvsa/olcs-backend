<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RefData Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity(repositoryClass="Olcs\Db\Entity\Repository\RefData")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ref_data",
 *    indexes={
 *        @ORM\Index(name="fk_ref_data_ref_data1_idx", columns={"parent_id"})
 *    }
 * )
 */
class RefData implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Id32Identity;

    /**
     * Parent
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * Pi
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Pi", mappedBy="piTypes", fetch="LAZY")
     */
    protected $pis;

    /**
     * Case
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Cases", mappedBy="categorys", fetch="LAZY")
     */
    protected $cases;

    /**
     * Impounding
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Impounding", mappedBy="impoundingLegislationTypes", fetch="LAZY")
     */
    protected $impoundings;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=512, nullable=true)
     * @Gedmo\Translatable
     */
    protected $description;

    /**
     * Ref data category id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ref_data_category_id", length=32, nullable=false)
     */
    protected $refDataCategoryId;

    /**
     * Olbs key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_key", length=20, nullable=true)
     */
    protected $olbsKey;

    /**
     * Display order
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="display_order", nullable=true)
     */
    protected $displayOrder;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->pis = new ArrayCollection();
        $this->cases = new ArrayCollection();
        $this->impoundings = new ArrayCollection();
    }

    /**
     * Set the parent
     *
     * @param \Olcs\Db\Entity\RefData $parent
     * @return RefData
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get the parent
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the pi
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return RefData
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return RefData
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return RefData
     */
    public function removePis($pis)
    {
        if ($this->pis->contains($pis)) {
            $this->pis->removeElement($pis);
        }

        return $this;
    }

    /**
     * Set the case
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return RefData
     */
    public function setCases($cases)
    {
        $this->cases = $cases;

        return $this;
    }

    /**
     * Get the cases
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCases()
    {
        return $this->cases;
    }

    /**
     * Add a cases
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return RefData
     */
    public function addCases($cases)
    {
        if ($cases instanceof ArrayCollection) {
            $this->cases = new ArrayCollection(
                array_merge(
                    $this->cases->toArray(),
                    $cases->toArray()
                )
            );
        } elseif (!$this->cases->contains($cases)) {
            $this->cases->add($cases);
        }

        return $this;
    }

    /**
     * Remove a cases
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return RefData
     */
    public function removeCases($cases)
    {
        if ($this->cases->contains($cases)) {
            $this->cases->removeElement($cases);
        }

        return $this;
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundings
     * @return RefData
     */
    public function removeImpoundings($impoundings)
    {
        if ($this->impoundings->contains($impoundings)) {
            $this->impoundings->removeElement($impoundings);
        }

        return $this;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return RefData
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the ref data category id
     *
     * @param string $refDataCategoryId
     * @return RefData
     */
    public function setRefDataCategoryId($refDataCategoryId)
    {
        $this->refDataCategoryId = $refDataCategoryId;

        return $this;
    }

    /**
     * Get the ref data category id
     *
     * @return string
     */
    public function getRefDataCategoryId()
    {
        return $this->refDataCategoryId;
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

    /**
     * Set the display order
     *
     * @param int $displayOrder
     * @return RefData
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    /**
     * Get the display order
     *
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }
}

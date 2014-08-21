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
     * @ORM\Column(type="string", name="description", length=100, nullable=true)
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
     * Initialise the collections
     */
    public function __construct()
    {
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
}

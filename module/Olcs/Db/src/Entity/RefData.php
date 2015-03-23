<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
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
 *        @ORM\Index(name="ix_ref_data_parent_id", columns={"parent_id"}),
 *        @ORM\Index(name="ix_ref_data_ref_data_category_id", columns={"ref_data_category_id"})
 *    }
 * )
 */
class RefData implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Id32Identity;

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
     * Display order
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="display_order", nullable=true)
     */
    protected $displayOrder;

    /**
     * Olbs key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_key", length=20, nullable=true)
     */
    protected $olbsKey;

    /**
     * Parent
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * Ref data category id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ref_data_category_id", length=32, nullable=false)
     */
    protected $refDataCategoryId;

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
}

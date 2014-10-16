<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ConvictionCategory Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="conviction_category",
 *    indexes={
 *        @ORM\Index(name="fk_conviction_category_conviction_category1_idx", columns={"parent_id"}),
 *        @ORM\Index(name="fk_conviction_category_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_conviction_category_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class ConvictionCategory implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Parent
     *
     * @var \Olcs\Db\Entity\ConvictionCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ConvictionCategory", fetch="LAZY")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=1024, nullable=false)
     */
    protected $description;

    /**
     * Set the parent
     *
     * @param \Olcs\Db\Entity\ConvictionCategory $parent
     * @return ConvictionCategory
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get the parent
     *
     * @return \Olcs\Db\Entity\ConvictionCategory
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return ConvictionCategory
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
}

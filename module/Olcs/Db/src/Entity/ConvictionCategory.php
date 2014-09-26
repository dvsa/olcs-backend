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
 *        @ORM\Index(name="IDX_74916A60727ACA70", columns={"parent_id"}),
 *        @ORM\Index(name="IDX_74916A60DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_74916A6065CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class ConvictionCategory implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\Description1024Field,
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
}

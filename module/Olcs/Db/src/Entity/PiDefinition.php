<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PiDefinition Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pi_definition",
 *    indexes={
 *        @ORM\Index(name="ix_pi_definition_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_pi_definition_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_pi_definition_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class PiDefinition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255Field,
        Traits\GoodsOrPsvManyToOne,
        Traits\IdIdentity,
        Traits\IsNiFieldAlt1,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\SectionCode20Field,
        Traits\CustomVersionField;

    /**
     * Pi definition category
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pi_definition_category", length=32, nullable=false)
     */
    protected $piDefinitionCategory;

    /**
     * Set the pi definition category
     *
     * @param string $piDefinitionCategory
     * @return PiDefinition
     */
    public function setPiDefinitionCategory($piDefinitionCategory)
    {
        $this->piDefinitionCategory = $piDefinitionCategory;

        return $this;
    }

    /**
     * Get the pi definition category
     *
     * @return string
     */
    public function getPiDefinitionCategory()
    {
        return $this->piDefinitionCategory;
    }
}

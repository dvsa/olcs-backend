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
 *        @ORM\Index(name="fk_pi_definition_ref_data1_idx", columns={"goods_or_psv"}),
 *        @ORM\Index(name="fk_pi_definition_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_definition_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class PiDefinition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\GoodsOrPsvManyToOne,
        Traits\Description255Field,
        Traits\IsNiFieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
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
     * Section code
     *
     * @var string
     *
     * @ORM\Column(type="string", name="section_code", length=20, nullable=false)
     */
    protected $sectionCode;

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

    /**
     * Set the section code
     *
     * @param string $sectionCode
     * @return PiDefinition
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
}

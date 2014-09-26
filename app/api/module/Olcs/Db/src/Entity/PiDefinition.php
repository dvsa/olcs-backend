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
 *        @ORM\Index(name="IDX_608702D3DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_608702D365CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_608702D3324926D6", columns={"goods_or_psv"})
 *    }
 * )
 */
class PiDefinition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\GoodsOrPsvManyToOne,
        Traits\Description255Field,
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
     * Is ni
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_ni", nullable=false)
     */
    protected $isNi;

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

    /**
     * Set the is ni
     *
     * @param string $isNi
     * @return PiDefinition
     */
    public function setIsNi($isNi)
    {
        $this->isNi = $isNi;

        return $this;
    }

    /**
     * Get the is ni
     *
     * @return string
     */
    public function getIsNi()
    {
        return $this->isNi;
    }
}

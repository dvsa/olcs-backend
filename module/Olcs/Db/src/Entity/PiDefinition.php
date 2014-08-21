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
 *        @ORM\Index(name="fk_pi_definition_pi_definition_category1_idx", columns={"pi_definition_category_id"})
 *    }
 * )
 */
class PiDefinition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\GoodsOrPsv3Field,
        Traits\IsNiField;

    /**
     * Pi definition category
     *
     * @var \Olcs\Db\Entity\PiDefinitionCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PiDefinitionCategory", fetch="LAZY")
     * @ORM\JoinColumn(name="pi_definition_category_id", referencedColumnName="id", nullable=false)
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
     * Definition
     *
     * @var string
     *
     * @ORM\Column(type="string", name="definition", length=255, nullable=false)
     */
    protected $definition;

    /**
     * Set the pi definition category
     *
     * @param \Olcs\Db\Entity\PiDefinitionCategory $piDefinitionCategory
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
     * @return \Olcs\Db\Entity\PiDefinitionCategory
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
     * Set the definition
     *
     * @param string $definition
     * @return PiDefinition
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * Get the definition
     *
     * @return string
     */
    public function getDefinition()
    {
        return $this->definition;
    }
}

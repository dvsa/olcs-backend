<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Section code50 field trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait SectionCode50Field
{
    /**
     * Section code
     *
     * @var string
     *
     * @ORM\Column(type="string", name="section_code", length=50, nullable=false)
     */
    protected $sectionCode;

    /**
     * Set the section code
     *
     * @param string $sectionCode
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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

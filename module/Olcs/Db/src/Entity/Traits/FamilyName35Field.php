<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Family name35 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait FamilyName35Field
{
    /**
     * Family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="family_name", length=35, nullable=true)
     */
    protected $familyName;

    /**
     * Set the family name
     *
     * @param string $familyName
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;

        return $this;
    }

    /**
     * Get the family name
     *
     * @return string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }
}

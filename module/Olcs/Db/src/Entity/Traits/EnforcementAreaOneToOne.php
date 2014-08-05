<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enforcement area one to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait EnforcementAreaOneToOne
{
    /**
     * Identifier - Enforcement area
     *
     * @var \Olcs\Db\Entity\EnforcementArea
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\EnforcementArea")
     * @ORM\JoinColumn(name="enforcement_area_id", referencedColumnName="id")
     */
    protected $enforcementArea;

    /**
     * Set the enforcement area
     *
     * @param \Olcs\Db\Entity\EnforcementArea $enforcementArea
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setEnforcementArea($enforcementArea)
    {
        $this->enforcementArea = $enforcementArea;

        return $this;
    }

    /**
     * Get the enforcement area
     *
     * @return \Olcs\Db\Entity\EnforcementArea
     */
    public function getEnforcementArea()
    {
        return $this->enforcementArea;
    }
}

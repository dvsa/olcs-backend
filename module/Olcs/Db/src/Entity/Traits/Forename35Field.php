<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Forename35 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait Forename35Field
{
    /**
     * Forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="forename", length=35, nullable=true)
     */
    protected $forename;

    /**
     * Set the forename
     *
     * @param string $forename
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setForename($forename)
    {
        $this->forename = $forename;

        return $this;
    }

    /**
     * Get the forename
     *
     * @return string
     */
    public function getForename()
    {
        return $this->forename;
    }
}

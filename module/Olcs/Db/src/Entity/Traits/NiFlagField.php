<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ni flag field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait NiFlagField
{
    /**
     * Ni flag
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="ni_flag", nullable=true)
     */
    protected $niFlag;

    /**
     * Set the ni flag
     *
     * @param string $niFlag
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNiFlag($niFlag)
    {
        $this->niFlag = $niFlag;

        return $this;
    }

    /**
     * Get the ni flag
     *
     * @return string
     */
    public function getNiFlag()
    {
        return $this->niFlag;
    }
}

<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is scan field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsScanField
{
    /**
     * Is scan
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_scan", nullable=false)
     */
    protected $isScan = 0;

    /**
     * Set the is scan
     *
     * @param boolean $isScan
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsScan($isScan)
    {
        $this->isScan = $isScan;

        return $this;
    }

    /**
     * Get the is scan
     *
     * @return boolean
     */
    public function getIsScan()
    {
        return $this->isScan;
    }
}

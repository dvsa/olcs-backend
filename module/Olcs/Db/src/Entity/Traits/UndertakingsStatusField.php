<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Undertakings status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait UndertakingsStatusField
{
    /**
     * Undertakings status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="undertakings_status", nullable=true)
     */
    protected $undertakingsStatus;

    /**
     * Set the undertakings status
     *
     * @param int $undertakingsStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setUndertakingsStatus($undertakingsStatus)
    {
        $this->undertakingsStatus = $undertakingsStatus;

        return $this;
    }

    /**
     * Get the undertakings status
     *
     * @return int
     */
    public function getUndertakingsStatus()
    {
        return $this->undertakingsStatus;
    }
}

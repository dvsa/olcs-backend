<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conditions undertakings status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait ConditionsUndertakingsStatusField
{
    /**
     * Conditions undertakings status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="conditions_undertakings_status", nullable=true)
     */
    protected $conditionsUndertakingsStatus;

    /**
     * Set the conditions undertakings status
     *
     * @param int $conditionsUndertakingsStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setConditionsUndertakingsStatus($conditionsUndertakingsStatus)
    {
        $this->conditionsUndertakingsStatus = $conditionsUndertakingsStatus;

        return $this;
    }

    /**
     * Get the conditions undertakings status
     *
     * @return int
     */
    public function getConditionsUndertakingsStatus()
    {
        return $this->conditionsUndertakingsStatus;
    }
}

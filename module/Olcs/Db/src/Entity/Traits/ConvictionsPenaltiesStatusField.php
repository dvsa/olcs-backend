<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Convictions penalties status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait ConvictionsPenaltiesStatusField
{
    /**
     * Convictions penalties status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="convictions_penalties_status", nullable=true)
     */
    protected $convictionsPenaltiesStatus;

    /**
     * Set the convictions penalties status
     *
     * @param int $convictionsPenaltiesStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setConvictionsPenaltiesStatus($convictionsPenaltiesStatus)
    {
        $this->convictionsPenaltiesStatus = $convictionsPenaltiesStatus;

        return $this;
    }

    /**
     * Get the convictions penalties status
     *
     * @return int
     */
    public function getConvictionsPenaltiesStatus()
    {
        return $this->convictionsPenaltiesStatus;
    }
}

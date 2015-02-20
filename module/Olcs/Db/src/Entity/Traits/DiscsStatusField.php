<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Discs status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait DiscsStatusField
{
    /**
     * Discs status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="discs_status", nullable=true)
     */
    protected $discsStatus;

    /**
     * Set the discs status
     *
     * @param int $discsStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDiscsStatus($discsStatus)
    {
        $this->discsStatus = $discsStatus;

        return $this;
    }

    /**
     * Get the discs status
     *
     * @return int
     */
    public function getDiscsStatus()
    {
        return $this->discsStatus;
    }
}

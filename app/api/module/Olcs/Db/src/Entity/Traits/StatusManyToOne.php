<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Status many to one trait
 *
 * Auto-Generated (Shared between 7 entities)
 */
trait StatusManyToOne
{
    /**
     * Status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=false)
     */
    protected $status;

    /**
     * Set the status
     *
     * @param \Olcs\Db\Entity\RefData $status
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getStatus()
    {
        return $this->status;
    }
}

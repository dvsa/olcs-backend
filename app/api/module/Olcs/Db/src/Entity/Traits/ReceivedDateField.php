<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Received date field trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait ReceivedDateField
{
    /**
     * Received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="received_date", nullable=true)
     */
    protected $receivedDate;

    /**
     * Set the received date
     *
     * @param \DateTime $receivedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setReceivedDate($receivedDate)
    {
        $this->receivedDate = $receivedDate;

        return $this;
    }

    /**
     * Get the received date
     *
     * @return \DateTime
     */
    public function getReceivedDate()
    {
        return $this->receivedDate;
    }
}

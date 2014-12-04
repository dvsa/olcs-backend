<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Granted date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait GrantedDateField
{
    /**
     * Granted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="granted_date", nullable=true)
     */
    protected $grantedDate;

    /**
     * Set the granted date
     *
     * @param \DateTime $grantedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setGrantedDate($grantedDate)
    {
        $this->grantedDate = $grantedDate;

        return $this;
    }

    /**
     * Get the granted date
     *
     * @return \DateTime
     */
    public function getGrantedDate()
    {
        return $this->grantedDate;
    }
}

<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Disqualification date field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait DisqualificationDateField
{
    /**
     * Disqualification date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="disqualification_date", nullable=true)
     */
    protected $disqualificationDate;

    /**
     * Set the disqualification date
     *
     * @param \DateTime $disqualificationDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDisqualificationDate($disqualificationDate)
    {
        $this->disqualificationDate = $disqualificationDate;

        return $this;
    }

    /**
     * Get the disqualification date
     *
     * @return \DateTime
     */
    public function getDisqualificationDate()
    {
        return $this->disqualificationDate;
    }
}

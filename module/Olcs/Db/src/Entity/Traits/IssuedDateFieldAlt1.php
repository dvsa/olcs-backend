<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Issued date field alt1 trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait IssuedDateFieldAlt1
{
    /**
     * Issued date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="issued_date", nullable=true)
     */
    protected $issuedDate;

    /**
     * Set the issued date
     *
     * @param \DateTime $issuedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIssuedDate($issuedDate)
    {
        $this->issuedDate = $issuedDate;

        return $this;
    }

    /**
     * Get the issued date
     *
     * @return \DateTime
     */
    public function getIssuedDate()
    {
        return $this->issuedDate;
    }
}

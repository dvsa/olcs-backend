<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Birth date field trait
 *
 * Auto-Generated (Shared between 4 entities)
 */
trait BirthDateField
{
    /**
     * Birth date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="birth_date", nullable=true)
     */
    protected $birthDate;

    /**
     * Set the birth date
     *
     * @param \DateTime $birthDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get the birth date
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }
}

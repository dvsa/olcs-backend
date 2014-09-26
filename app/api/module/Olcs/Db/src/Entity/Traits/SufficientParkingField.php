<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sufficient parking field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait SufficientParkingField
{
    /**
     * Sufficient parking
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sufficient_parking", nullable=false)
     */
    protected $sufficientParking;

    /**
     * Set the sufficient parking
     *
     * @param string $sufficientParking
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setSufficientParking($sufficientParking)
    {
        $this->sufficientParking = $sufficientParking;

        return $this;
    }

    /**
     * Get the sufficient parking
     *
     * @return string
     */
    public function getSufficientParking()
    {
        return $this->sufficientParking;
    }
}

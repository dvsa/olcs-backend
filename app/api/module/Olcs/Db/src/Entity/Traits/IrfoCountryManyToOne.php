<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Irfo country many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IrfoCountryManyToOne
{
    /**
     * Irfo country
     *
     * @var \Olcs\Db\Entity\IrfoCountry
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoCountry", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_country_id", referencedColumnName="id")
     */
    protected $irfoCountry;

    /**
     * Set the irfo country
     *
     * @param \Olcs\Db\Entity\IrfoCountry $irfoCountry
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIrfoCountry($irfoCountry)
    {
        $this->irfoCountry = $irfoCountry;

        return $this;
    }

    /**
     * Get the irfo country
     *
     * @return \Olcs\Db\Entity\IrfoCountry
     */
    public function getIrfoCountry()
    {
        return $this->irfoCountry;
    }

}

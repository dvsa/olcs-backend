<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country code many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait CountryCodeManyToOne
{
    /**
     * Country code
     *
     * @var \Olcs\Db\Entity\Country
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Country", fetch="LAZY")
     * @ORM\JoinColumn(name="country_code", referencedColumnName="id")
     */
    protected $countryCode;

    /**
     * Set the country code
     *
     * @param \Olcs\Db\Entity\Country $countryCode
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get the country code
     *
     * @return \Olcs\Db\Entity\Country
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

}

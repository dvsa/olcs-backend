<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Exemption details255 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait ExemptionDetails255Field
{
    /**
     * Exemption details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="exemption_details", length=255, nullable=true)
     */
    protected $exemptionDetails;

    /**
     * Set the exemption details
     *
     * @param string $exemptionDetails
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setExemptionDetails($exemptionDetails)
    {
        $this->exemptionDetails = $exemptionDetails;

        return $this;
    }

    /**
     * Get the exemption details
     *
     * @return string
     */
    public function getExemptionDetails()
    {
        return $this->exemptionDetails;
    }
}

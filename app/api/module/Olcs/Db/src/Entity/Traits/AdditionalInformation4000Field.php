<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Additional information4000 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait AdditionalInformation4000Field
{
    /**
     * Additional information
     *
     * @var string
     *
     * @ORM\Column(type="string", name="additional_information", length=4000, nullable=true)
     */
    protected $additionalInformation;

    /**
     * Set the additional information
     *
     * @param string $additionalInformation
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAdditionalInformation($additionalInformation)
    {
        $this->additionalInformation = $additionalInformation;

        return $this;
    }

    /**
     * Get the additional information
     *
     * @return string
     */
    public function getAdditionalInformation()
    {
        return $this->additionalInformation;
    }
}

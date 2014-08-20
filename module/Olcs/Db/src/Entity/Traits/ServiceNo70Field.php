<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service no70 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait ServiceNo70Field
{
    /**
     * Service no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="service_no", length=70, nullable=true)
     */
    protected $serviceNo;

    /**
     * Set the service no
     *
     * @param string $serviceNo
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setServiceNo($serviceNo)
    {
        $this->serviceNo = $serviceNo;

        return $this;
    }

    /**
     * Get the service no
     *
     * @return string
     */
    public function getServiceNo()
    {
        return $this->serviceNo;
    }
}

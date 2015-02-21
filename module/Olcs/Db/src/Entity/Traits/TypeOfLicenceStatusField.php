<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Type of licence status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TypeOfLicenceStatusField
{
    /**
     * Type of licence status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="type_of_licence_status", nullable=true)
     */
    protected $typeOfLicenceStatus;

    /**
     * Set the type of licence status
     *
     * @param int $typeOfLicenceStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTypeOfLicenceStatus($typeOfLicenceStatus)
    {
        $this->typeOfLicenceStatus = $typeOfLicenceStatus;

        return $this;
    }

    /**
     * Get the type of licence status
     *
     * @return int
     */
    public function getTypeOfLicenceStatus()
    {
        return $this->typeOfLicenceStatus;
    }
}

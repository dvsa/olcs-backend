<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Previous licence type many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PreviousLicenceTypeManyToOne
{
    /**
     * Previous licence type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="previous_licence_type", referencedColumnName="id", nullable=false)
     */
    protected $previousLicenceType;

    /**
     * Set the previous licence type
     *
     * @param \Olcs\Db\Entity\RefData $previousLicenceType
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPreviousLicenceType($previousLicenceType)
    {
        $this->previousLicenceType = $previousLicenceType;

        return $this;
    }

    /**
     * Get the previous licence type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPreviousLicenceType()
    {
        return $this->previousLicenceType;
    }
}

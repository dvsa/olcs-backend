<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Licence type many to one alt1 trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait LicenceTypeManyToOneAlt1
{
    /**
     * Licence type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="licence_type", referencedColumnName="id", nullable=false)
     */
    protected $licenceType;

    /**
     * Set the licence type
     *
     * @param \Olcs\Db\Entity\RefData $licenceType
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLicenceType($licenceType)
    {
        $this->licenceType = $licenceType;

        return $this;
    }

    /**
     * Get the licence type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getLicenceType()
    {
        return $this->licenceType;
    }
}

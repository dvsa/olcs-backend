<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Licence type many to one trait
 *
 * Auto-Generated (Shared between 4 entities)
 */
trait LicenceTypeManyToOne
{
    /**
     * Licence type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_type", referencedColumnName="id")
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

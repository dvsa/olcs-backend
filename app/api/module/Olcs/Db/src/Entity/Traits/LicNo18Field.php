<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lic no18 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait LicNo18Field
{
    /**
     * Lic no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="lic_no", length=18, nullable=true)
     */
    protected $licNo;

    /**
     * Set the lic no
     *
     * @param string $licNo
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLicNo($licNo)
    {
        $this->licNo = $licNo;

        return $this;
    }

    /**
     * Get the lic no
     *
     * @return string
     */
    public function getLicNo()
    {
        return $this->licNo;
    }

}

<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * LicenceNoGen Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="licence_no_gen",
 *    indexes={
 *        @ORM\Index(name="ix_licence_no_gen_licence_id", columns={"licence_id"})
 *    }
 * )
 */
class LicenceNoGen implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Application $licence
     * @return LicenceNoGen
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getLicence()
    {
        return $this->licence;
    }
}

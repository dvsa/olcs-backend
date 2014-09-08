<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vrm20 field trait
 *
 * Auto-Generated (Shared between 6 entities)
 */
trait Vrm20Field
{
    /**
     * Vrm
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vrm", length=20, nullable=true)
     */
    protected $vrm;

    /**
     * Set the vrm
     *
     * @param string $vrm
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVrm($vrm)
    {
        $this->vrm = $vrm;

        return $this;
    }

    /**
     * Get the vrm
     *
     * @return string
     */
    public function getVrm()
    {
        return $this->vrm;
    }
}

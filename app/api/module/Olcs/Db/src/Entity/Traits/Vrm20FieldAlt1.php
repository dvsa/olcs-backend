<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vrm20 field alt1 trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait Vrm20FieldAlt1
{
    /**
     * Vrm
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vrm", length=20, nullable=false)
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

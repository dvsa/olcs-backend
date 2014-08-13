<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Irfo psv auth many to one trait
 *
 * Auto-Generated (Shared between 4 entities)
 */
trait IrfoPsvAuthManyToOne
{
    /**
     * Irfo psv auth
     *
     * @var \Olcs\Db\Entity\IrfoPsvAuth
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoPsvAuth", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_psv_auth_id", referencedColumnName="id")
     */
    protected $irfoPsvAuth;

    /**
     * Set the irfo psv auth
     *
     * @param \Olcs\Db\Entity\IrfoPsvAuth $irfoPsvAuth
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIrfoPsvAuth($irfoPsvAuth)
    {
        $this->irfoPsvAuth = $irfoPsvAuth;

        return $this;
    }

    /**
     * Get the irfo psv auth
     *
     * @return \Olcs\Db\Entity\IrfoPsvAuth
     */
    public function getIrfoPsvAuth()
    {
        return $this->irfoPsvAuth;
    }

}

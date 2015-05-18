<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * IrfoPsvAuthNumber Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_psv_auth_number",
 *    indexes={
 *        @ORM\Index(name="ix_irfo_psv_auth_number_irfo_psv_auth_id", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_number_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_number_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_irfo_psv_auth_number_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class IrfoPsvAuthNumber implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Name70Field,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Irfo psv auth
     *
     * @var \Olcs\Db\Entity\IrfoPsvAuth
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoPsvAuth", inversedBy="irfoPsvAuthNumbers")
     * @ORM\JoinColumn(name="irfo_psv_auth_id", referencedColumnName="id", nullable=false)
     */
    protected $irfoPsvAuth;

    /**
     * Set the irfo psv auth
     *
     * @param \Olcs\Db\Entity\IrfoPsvAuth $irfoPsvAuth
     * @return IrfoPsvAuthNumber
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

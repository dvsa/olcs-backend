<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * IrfoTransitCountry Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_transit_country",
 *    indexes={
 *        @ORM\Index(name="fk_irfo_transit_country_irfo_psv_auth1_idx", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="fk_irfo_transit_country_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_irfo_transit_country_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class IrfoTransitCountry implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description45Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Irfo psv auth
     *
     * @var \Olcs\Db\Entity\IrfoPsvAuth
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoPsvAuth", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_psv_auth_id", referencedColumnName="id", nullable=false)
     */
    protected $irfoPsvAuth;


    /**
     * Set the irfo psv auth
     *
     * @param \Olcs\Db\Entity\IrfoPsvAuth $irfoPsvAuth
     * @return IrfoTransitCountry
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

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
 *        @ORM\Index(name="IDX_FE3794FA4425C407", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="IDX_FE3794FADE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_FE3794FA65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class IrfoTransitCountry implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
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
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=45, nullable=false)
     */
    protected $description;

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

    /**
     * Set the description
     *
     * @param string $description
     * @return IrfoTransitCountry
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}

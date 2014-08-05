<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * IrfoVehicle Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_vehicle",
 *    indexes={
 *        @ORM\Index(name="fk_irfo_vehicle_irfo_psv_auth1_idx", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="fk_irfo_vehicle_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_irfo_vehicle_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_irfo_vehicle_irfo_gv_permit1_idx", columns={"irfo_gv_permit_id"})
 *    }
 * )
 */
class IrfoVehicle implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\IrfoGvPermitManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\IrfoPsvAuthManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField;

    /**
     * Coc a
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="coc_a", nullable=false)
     */
    protected $cocA = 0;

    /**
     * Coc b
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="coc_b", nullable=false)
     */
    protected $cocB = 0;

    /**
     * Coc c
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="coc_c", nullable=false)
     */
    protected $cocC = 0;

    /**
     * Coc d
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="coc_d", nullable=false)
     */
    protected $cocD = 0;

    /**
     * Coc t
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="coc_t", nullable=false)
     */
    protected $cocT = 0;

    /**
     * Vrm
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vrm", length=20, nullable=false)
     */
    protected $vrm;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=true)
     */
    protected $version = 1;

    /**
     * Set the coc a
     *
     * @param boolean $cocA
     * @return \Olcs\Db\Entity\IrfoVehicle
     */
    public function setCocA($cocA)
    {
        $this->cocA = $cocA;

        return $this;
    }

    /**
     * Get the coc a
     *
     * @return boolean
     */
    public function getCocA()
    {
        return $this->cocA;
    }

    /**
     * Set the coc b
     *
     * @param boolean $cocB
     * @return \Olcs\Db\Entity\IrfoVehicle
     */
    public function setCocB($cocB)
    {
        $this->cocB = $cocB;

        return $this;
    }

    /**
     * Get the coc b
     *
     * @return boolean
     */
    public function getCocB()
    {
        return $this->cocB;
    }

    /**
     * Set the coc c
     *
     * @param boolean $cocC
     * @return \Olcs\Db\Entity\IrfoVehicle
     */
    public function setCocC($cocC)
    {
        $this->cocC = $cocC;

        return $this;
    }

    /**
     * Get the coc c
     *
     * @return boolean
     */
    public function getCocC()
    {
        return $this->cocC;
    }

    /**
     * Set the coc d
     *
     * @param boolean $cocD
     * @return \Olcs\Db\Entity\IrfoVehicle
     */
    public function setCocD($cocD)
    {
        $this->cocD = $cocD;

        return $this;
    }

    /**
     * Get the coc d
     *
     * @return boolean
     */
    public function getCocD()
    {
        return $this->cocD;
    }

    /**
     * Set the coc t
     *
     * @param boolean $cocT
     * @return \Olcs\Db\Entity\IrfoVehicle
     */
    public function setCocT($cocT)
    {
        $this->cocT = $cocT;

        return $this;
    }

    /**
     * Get the coc t
     *
     * @return boolean
     */
    public function getCocT()
    {
        return $this->cocT;
    }

    /**
     * Set the vrm
     *
     * @param string $vrm
     * @return \Olcs\Db\Entity\IrfoVehicle
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

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\IrfoVehicle
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}

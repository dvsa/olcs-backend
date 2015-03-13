<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * LocalAuthority Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="local_authority",
 *    indexes={
 *        @ORM\Index(name="ix_local_authority_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_local_authority_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_local_authority_traffic_area_id", columns={"traffic_area_id"})
 *    }
 * )
 */
class LocalAuthority implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255Field,
        Traits\EmailAddress45Field,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\TrafficAreaManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Bus reg
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\BusReg", mappedBy="localAuthoritys")
     */
    protected $busRegs;

    /**
     * Naptan code
     *
     * @var string
     *
     * @ORM\Column(type="string", name="naptan_code", length=3, nullable=true)
     */
    protected $naptanCode;

    /**
     * Txc name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="txc_name", length=255, nullable=true)
     */
    protected $txcName;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->busRegs = new ArrayCollection();
    }

    /**
     * Set the bus reg
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $busRegs
     * @return LocalAuthority
     */
    public function setBusRegs($busRegs)
    {
        $this->busRegs = $busRegs;

        return $this;
    }

    /**
     * Get the bus regs
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getBusRegs()
    {
        return $this->busRegs;
    }

    /**
     * Add a bus regs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $busRegs
     * @return LocalAuthority
     */
    public function addBusRegs($busRegs)
    {
        if ($busRegs instanceof ArrayCollection) {
            $this->busRegs = new ArrayCollection(
                array_merge(
                    $this->busRegs->toArray(),
                    $busRegs->toArray()
                )
            );
        } elseif (!$this->busRegs->contains($busRegs)) {
            $this->busRegs->add($busRegs);
        }

        return $this;
    }

    /**
     * Remove a bus regs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $busRegs
     * @return LocalAuthority
     */
    public function removeBusRegs($busRegs)
    {
        if ($this->busRegs->contains($busRegs)) {
            $this->busRegs->removeElement($busRegs);
        }

        return $this;
    }

    /**
     * Set the naptan code
     *
     * @param string $naptanCode
     * @return LocalAuthority
     */
    public function setNaptanCode($naptanCode)
    {
        $this->naptanCode = $naptanCode;

        return $this;
    }

    /**
     * Get the naptan code
     *
     * @return string
     */
    public function getNaptanCode()
    {
        return $this->naptanCode;
    }

    /**
     * Set the txc name
     *
     * @param string $txcName
     * @return LocalAuthority
     */
    public function setTxcName($txcName)
    {
        $this->txcName = $txcName;

        return $this;
    }

    /**
     * Get the txc name
     *
     * @return string
     */
    public function getTxcName()
    {
        return $this->txcName;
    }
}

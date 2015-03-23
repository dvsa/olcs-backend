<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * VoidDisc Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="void_disc",
 *    indexes={
 *        @ORM\Index(name="ix_void_disc_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_void_disc_licence_type", columns={"licence_type"}),
 *        @ORM\Index(name="ix_void_disc_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_void_disc_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_void_disc_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_void_disc_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class VoidDisc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\GoodsOrPsvManyToOneAlt1,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceTypeManyToOneAlt1,
        Traits\OlbsKeyField,
        Traits\TrafficAreaManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Is ni self serve
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_ni_self_serve", nullable=false, options={"default": 0})
     */
    protected $isNiSelfServe = 0;

    /**
     * Is self serve
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_self_serve", nullable=false, options={"default": 0})
     */
    protected $isSelfServe = 0;

    /**
     * Serial end
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="serial_end", nullable=true)
     */
    protected $serialEnd;

    /**
     * Serial start
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="serial_start", nullable=true)
     */
    protected $serialStart;

    /**
     * Set the is ni self serve
     *
     * @param boolean $isNiSelfServe
     * @return VoidDisc
     */
    public function setIsNiSelfServe($isNiSelfServe)
    {
        $this->isNiSelfServe = $isNiSelfServe;

        return $this;
    }

    /**
     * Get the is ni self serve
     *
     * @return boolean
     */
    public function getIsNiSelfServe()
    {
        return $this->isNiSelfServe;
    }

    /**
     * Set the is self serve
     *
     * @param boolean $isSelfServe
     * @return VoidDisc
     */
    public function setIsSelfServe($isSelfServe)
    {
        $this->isSelfServe = $isSelfServe;

        return $this;
    }

    /**
     * Get the is self serve
     *
     * @return boolean
     */
    public function getIsSelfServe()
    {
        return $this->isSelfServe;
    }

    /**
     * Set the serial end
     *
     * @param int $serialEnd
     * @return VoidDisc
     */
    public function setSerialEnd($serialEnd)
    {
        $this->serialEnd = $serialEnd;

        return $this;
    }

    /**
     * Get the serial end
     *
     * @return int
     */
    public function getSerialEnd()
    {
        return $this->serialEnd;
    }

    /**
     * Set the serial start
     *
     * @param int $serialStart
     * @return VoidDisc
     */
    public function setSerialStart($serialStart)
    {
        $this->serialStart = $serialStart;

        return $this;
    }

    /**
     * Get the serial start
     *
     * @return int
     */
    public function getSerialStart()
    {
        return $this->serialStart;
    }
}

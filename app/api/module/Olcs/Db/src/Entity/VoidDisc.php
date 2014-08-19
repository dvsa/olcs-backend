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
 *        @ORM\Index(name="fk_void_disc_ref_data1_idx", columns={"goods_or_psv"}),
 *        @ORM\Index(name="fk_void_disc_ref_data2_idx", columns={"licence_type"}),
 *        @ORM\Index(name="fk_void_disc_traffic_area1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_void_disc_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_void_disc_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class VoidDisc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\GoodsOrPsvManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Licence type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_type", referencedColumnName="id", nullable=false)
     */
    protected $licenceType;

    /**
     * Serial start
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="serial_start", nullable=true)
     */
    protected $serialStart;

    /**
     * Serial end
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="serial_end", nullable=true)
     */
    protected $serialEnd;


    /**
     * Set the licence type
     *
     * @param \Olcs\Db\Entity\RefData $licenceType
     * @return VoidDisc
     */
    public function setLicenceType($licenceType)
    {
        $this->licenceType = $licenceType;

        return $this;
    }

    /**
     * Get the licence type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getLicenceType()
    {
        return $this->licenceType;
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
}

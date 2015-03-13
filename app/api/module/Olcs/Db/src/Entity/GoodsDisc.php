<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * GoodsDisc Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="goods_disc",
 *    indexes={
 *        @ORM\Index(name="ix_goods_disc_licence_vehicle_id", columns={"licence_vehicle_id"}),
 *        @ORM\Index(name="ix_goods_disc_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_goods_disc_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_goods_disc_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class GoodsDisc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CeasedDateField,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DiscNo50Field,
        Traits\IdIdentity,
        Traits\IsInterimField,
        Traits\IsPrintingField,
        Traits\IssuedDateField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Is copy
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_copy", nullable=false, options={"default": 0})
     */
    protected $isCopy = 0;

    /**
     * Licence vehicle
     *
     * @var \Olcs\Db\Entity\LicenceVehicle
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\LicenceVehicle", inversedBy="goodsDiscs")
     * @ORM\JoinColumn(name="licence_vehicle_id", referencedColumnName="id", nullable=false)
     */
    protected $licenceVehicle;

    /**
     * Reprint required
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="reprint_required", nullable=false, options={"default": 0})
     */
    protected $reprintRequired = 0;

    /**
     * Set the is copy
     *
     * @param string $isCopy
     * @return GoodsDisc
     */
    public function setIsCopy($isCopy)
    {
        $this->isCopy = $isCopy;

        return $this;
    }

    /**
     * Get the is copy
     *
     * @return string
     */
    public function getIsCopy()
    {
        return $this->isCopy;
    }

    /**
     * Set the licence vehicle
     *
     * @param \Olcs\Db\Entity\LicenceVehicle $licenceVehicle
     * @return GoodsDisc
     */
    public function setLicenceVehicle($licenceVehicle)
    {
        $this->licenceVehicle = $licenceVehicle;

        return $this;
    }

    /**
     * Get the licence vehicle
     *
     * @return \Olcs\Db\Entity\LicenceVehicle
     */
    public function getLicenceVehicle()
    {
        return $this->licenceVehicle;
    }

    /**
     * Set the reprint required
     *
     * @param string $reprintRequired
     * @return GoodsDisc
     */
    public function setReprintRequired($reprintRequired)
    {
        $this->reprintRequired = $reprintRequired;

        return $this;
    }

    /**
     * Get the reprint required
     *
     * @return string
     */
    public function getReprintRequired()
    {
        return $this->reprintRequired;
    }
}

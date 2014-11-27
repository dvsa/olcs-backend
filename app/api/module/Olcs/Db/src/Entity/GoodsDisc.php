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
 *        @ORM\Index(name="fk_goods_disc_licence_vehicle1_idx", columns={"licence_vehicle_id"}),
 *        @ORM\Index(name="fk_goods_disc_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_goods_disc_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class GoodsDisc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\DiscNo50Field,
        Traits\IssuedDateField,
        Traits\CeasedDateField,
        Traits\IsPrintingField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Licence vehicle
     *
     * @var \Olcs\Db\Entity\LicenceVehicle
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\LicenceVehicle", fetch="LAZY", inversedBy="goodsDiscs")
     * @ORM\JoinColumn(name="licence_vehicle_id", referencedColumnName="id", nullable=false)
     */
    protected $licenceVehicle;

    /**
     * Is copy
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_copy", nullable=false)
     */
    protected $isCopy = 0;

    /**
     * Is interim
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_interim", nullable=false)
     */
    protected $isInterim = 0;

    /**
     * Reprint required
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="reprint_required", nullable=false)
     */
    protected $reprintRequired = 0;

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
     * Set the is interim
     *
     * @param string $isInterim
     * @return GoodsDisc
     */
    public function setIsInterim($isInterim)
    {
        $this->isInterim = $isInterim;

        return $this;
    }

    /**
     * Get the is interim
     *
     * @return string
     */
    public function getIsInterim()
    {
        return $this->isInterim;
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

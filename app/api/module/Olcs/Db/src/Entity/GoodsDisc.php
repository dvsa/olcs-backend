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
 *        @ORM\Index(name="IDX_31474EB2F75B4EBD", columns={"licence_vehicle_id"}),
 *        @ORM\Index(name="IDX_31474EB2FC21D85", columns={"removal_explanation"}),
 *        @ORM\Index(name="IDX_31474EB2DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_31474EB2D45B0D47", columns={"removal_reason"}),
 *        @ORM\Index(name="IDX_31474EB265CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class GoodsDisc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\RemovalExplanationManyToOne,
        Traits\CreatedByManyToOne,
        Traits\RemovalReasonManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\DiscNo50Field,
        Traits\IssuedDateField,
        Traits\CeasedDateField,
        Traits\IsInterimField,
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
    protected $isCopy;

    /**
     * Reprint required
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="reprint_required", nullable=false)
     */
    protected $reprintRequired;

    /**
     * Is printing
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_printing", nullable=false)
     */
    protected $isPrinting;

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

    /**
     * Set the is printing
     *
     * @param string $isPrinting
     * @return GoodsDisc
     */
    public function setIsPrinting($isPrinting)
    {
        $this->isPrinting = $isPrinting;

        return $this;
    }

    /**
     * Get the is printing
     *
     * @return string
     */
    public function getIsPrinting()
    {
        return $this->isPrinting;
    }
}

<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesTrait;
use Dvsa\Olcs\Api\Entity\Traits\CreatedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\ModifiedOnTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IrhpPermitWindow Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irhp_permit_window",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_windows_irhp_permit_stocks1_idx",
     *     columns={"irhp_permit_stock_id"}),
 *        @ORM\Index(name="fk_irhp_permit_window_last_modified_by_user_id",
     *     columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_irhp_permit_window_created_by_user_id", columns={"created_by"})
 *    }
 * )
 */
abstract class AbstractIrhpPermitWindow implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesTrait;
    use CreatedOnTrait;
    use ModifiedOnTrait;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="create")
     */
    protected $createdBy;

    /**
     * Days for payment
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="days_for_payment", nullable=true)
     */
    protected $daysForPayment;

    /**
     * End date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="end_date", nullable=false)
     */
    protected $endDate;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Irhp permit stock
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock",
     *     fetch="LAZY",
     *     inversedBy="irhpPermitWindows"
     * )
     * @ORM\JoinColumn(name="irhp_permit_stock_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitStock;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="update")
     */
    protected $lastModifiedBy;

    /**
     * Start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="start_date", nullable=false)
     */
    protected $startDate;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return IrhpPermitWindow
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the days for payment
     *
     * @param int $daysForPayment new value being set
     *
     * @return IrhpPermitWindow
     */
    public function setDaysForPayment($daysForPayment)
    {
        $this->daysForPayment = $daysForPayment;

        return $this;
    }

    /**
     * Get the days for payment
     *
     * @return int
     */
    public function getDaysForPayment()
    {
        return $this->daysForPayment;
    }

    /**
     * Set the end date
     *
     * @param \DateTime $endDate new value being set
     *
     * @return IrhpPermitWindow
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the end date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getEndDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->endDate);
        }

        return $this->endDate;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return IrhpPermitWindow
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the irhp permit stock
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock $irhpPermitStock entity being set as the value
     *
     * @return IrhpPermitWindow
     */
    public function setIrhpPermitStock($irhpPermitStock)
    {
        $this->irhpPermitStock = $irhpPermitStock;

        return $this;
    }

    /**
     * Get the irhp permit stock
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock
     */
    public function getIrhpPermitStock()
    {
        return $this->irhpPermitStock;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return IrhpPermitWindow
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the start date
     *
     * @param \DateTime $startDate new value being set
     *
     * @return IrhpPermitWindow
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the start date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getStartDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->startDate);
        }

        return $this->startDate;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return IrhpPermitWindow
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

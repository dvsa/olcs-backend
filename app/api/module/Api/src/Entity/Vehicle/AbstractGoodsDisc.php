<?php

namespace Dvsa\Olcs\Api\Entity\Vehicle;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GoodsDisc Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="goods_disc",
 *    indexes={
 *        @ORM\Index(name="ix_goods_disc_licence_vehicle_id", columns={"licence_vehicle_id"}),
 *        @ORM\Index(name="ix_goods_disc_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_goods_disc_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_goods_disc_ceased_date", columns={"ceased_date"}),
 *        @ORM\Index(name="ix_goods_disc_issued_date", columns={"issued_date"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_goods_disc_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
abstract class AbstractGoodsDisc implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Ceased date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="ceased_date", nullable=true)
     */
    protected $ceasedDate;

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
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Disc no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="disc_no", length=50, nullable=true)
     */
    protected $discNo;

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
     * Is copy
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_copy", nullable=false, options={"default": 0})
     */
    protected $isCopy = 0;

    /**
     * Is interim
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_interim", nullable=false, options={"default": 0})
     */
    protected $isInterim = 0;

    /**
     * Is printing
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_printing", nullable=false, options={"default": 0})
     */
    protected $isPrinting = 0;

    /**
     * Issued date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="issued_date", nullable=true)
     */
    protected $issuedDate;

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
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Licence vehicle
     *
     * @var \Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle",
     *     fetch="LAZY",
     *     inversedBy="goodsDiscs"
     * )
     * @ORM\JoinColumn(name="licence_vehicle_id", referencedColumnName="id", nullable=false)
     */
    protected $licenceVehicle;

    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Reprint required
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="reprint_required", nullable=false, options={"default": 0})
     */
    protected $reprintRequired = 0;

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
     * Set the ceased date
     *
     * @param \DateTime $ceasedDate
     * @return GoodsDisc
     */
    public function setCeasedDate($ceasedDate)
    {
        $this->ceasedDate = $ceasedDate;

        return $this;
    }

    /**
     * Get the ceased date
     *
     * @return \DateTime
     */
    public function getCeasedDate()
    {
        return $this->ceasedDate;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return GoodsDisc
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
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return GoodsDisc
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the disc no
     *
     * @param string $discNo
     * @return GoodsDisc
     */
    public function setDiscNo($discNo)
    {
        $this->discNo = $discNo;

        return $this;
    }

    /**
     * Get the disc no
     *
     * @return string
     */
    public function getDiscNo()
    {
        return $this->discNo;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return GoodsDisc
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

    /**
     * Set the issued date
     *
     * @param \DateTime $issuedDate
     * @return GoodsDisc
     */
    public function setIssuedDate($issuedDate)
    {
        $this->issuedDate = $issuedDate;

        return $this;
    }

    /**
     * Get the issued date
     *
     * @return \DateTime
     */
    public function getIssuedDate()
    {
        return $this->issuedDate;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return GoodsDisc
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
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return GoodsDisc
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the licence vehicle
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle $licenceVehicle
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
     * @return \Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle
     */
    public function getLicenceVehicle()
    {
        return $this->licenceVehicle;
    }

    /**
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return GoodsDisc
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
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
     * Set the version
     *
     * @param int $version
     * @return GoodsDisc
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

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}

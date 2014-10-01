<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LicenceVehicle Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="licence_vehicle",
 *    indexes={
 *        @ORM\Index(name="IDX_D5372E35545317D1", columns={"vehicle_id"}),
 *        @ORM\Index(name="IDX_D5372E3526EF07C9", columns={"licence_id"}),
 *        @ORM\Index(name="IDX_D5372E35D45B0D47", columns={"removal_reason"}),
 *        @ORM\Index(name="IDX_D5372E353E030ACD", columns={"application_id"}),
 *        @ORM\Index(name="IDX_D5372E3565CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_D5372E35DE12AB56", columns={"created_by"})
 *    }
 * )
 */
class LicenceVehicle implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\RemovalReasonManyToOne,
        Traits\ApplicationManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\ReceivedDateFieldAlt1,
        Traits\CustomDeletedDateField,
        Traits\ViAction1Field,
        Traits\SpecifiedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Vehicle
     *
     * @var \Olcs\Db\Entity\Vehicle
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Vehicle", fetch="LAZY", inversedBy="licenceVehicles")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id", nullable=false)
     */
    protected $vehicle;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY", inversedBy="licenceVehicles")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Removal
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="removal", nullable=true)
     */
    protected $removal;

    /**
     * Removal letter seed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="removal_letter_seed_date", nullable=true)
     */
    protected $removalLetterSeedDate;

    /**
     * Is interim
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="is_interim", nullable=true)
     */
    protected $isInterim;

    /**
     * Warning letter seed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="warning_letter_seed_date", nullable=true)
     */
    protected $warningLetterSeedDate;

    /**
     * Warning letter sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="warning_letter_sent_date", nullable=true)
     */
    protected $warningLetterSentDate;

    /**
     * Goods disc
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\GoodsDisc", mappedBy="licenceVehicle")
     * @ORM\OrderBy({"createdOn" = "DESC"})
     */
    protected $goodsDiscs;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->goodsDiscs = new ArrayCollection();
    }

    /**
     * Set the vehicle
     *
     * @param \Olcs\Db\Entity\Vehicle $vehicle
     * @return LicenceVehicle
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * Get the vehicle
     *
     * @return \Olcs\Db\Entity\Vehicle
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return LicenceVehicle
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the removal
     *
     * @param string $removal
     * @return LicenceVehicle
     */
    public function setRemoval($removal)
    {
        $this->removal = $removal;

        return $this;
    }

    /**
     * Get the removal
     *
     * @return string
     */
    public function getRemoval()
    {
        return $this->removal;
    }

    /**
     * Set the removal letter seed date
     *
     * @param \DateTime $removalLetterSeedDate
     * @return LicenceVehicle
     */
    public function setRemovalLetterSeedDate($removalLetterSeedDate)
    {
        $this->removalLetterSeedDate = $removalLetterSeedDate;

        return $this;
    }

    /**
     * Get the removal letter seed date
     *
     * @return \DateTime
     */
    public function getRemovalLetterSeedDate()
    {
        return $this->removalLetterSeedDate;
    }

    /**
     * Set the is interim
     *
     * @param int $isInterim
     * @return LicenceVehicle
     */
    public function setIsInterim($isInterim)
    {
        $this->isInterim = $isInterim;

        return $this;
    }

    /**
     * Get the is interim
     *
     * @return int
     */
    public function getIsInterim()
    {
        return $this->isInterim;
    }

    /**
     * Set the warning letter seed date
     *
     * @param \DateTime $warningLetterSeedDate
     * @return LicenceVehicle
     */
    public function setWarningLetterSeedDate($warningLetterSeedDate)
    {
        $this->warningLetterSeedDate = $warningLetterSeedDate;

        return $this;
    }

    /**
     * Get the warning letter seed date
     *
     * @return \DateTime
     */
    public function getWarningLetterSeedDate()
    {
        return $this->warningLetterSeedDate;
    }

    /**
     * Set the warning letter sent date
     *
     * @param \DateTime $warningLetterSentDate
     * @return LicenceVehicle
     */
    public function setWarningLetterSentDate($warningLetterSentDate)
    {
        $this->warningLetterSentDate = $warningLetterSentDate;

        return $this;
    }

    /**
     * Get the warning letter sent date
     *
     * @return \DateTime
     */
    public function getWarningLetterSentDate()
    {
        return $this->warningLetterSentDate;
    }

    /**
     * Set the goods disc
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $goodsDiscs
     * @return LicenceVehicle
     */
    public function setGoodsDiscs($goodsDiscs)
    {
        $this->goodsDiscs = $goodsDiscs;

        return $this;
    }

    /**
     * Get the goods discs
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getGoodsDiscs()
    {
        return $this->goodsDiscs;
    }

    /**
     * Add a goods discs
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $goodsDiscs
     * @return LicenceVehicle
     */
    public function addGoodsDiscs($goodsDiscs)
    {
        if ($goodsDiscs instanceof ArrayCollection) {
            $this->goodsDiscs = new ArrayCollection(
                array_merge(
                    $this->goodsDiscs->toArray(),
                    $goodsDiscs->toArray()
                )
            );
        } elseif (!$this->goodsDiscs->contains($goodsDiscs)) {
            $this->goodsDiscs->add($goodsDiscs);
        }

        return $this;
    }

    /**
     * Remove a goods discs
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $goodsDiscs
     * @return LicenceVehicle
     */
    public function removeGoodsDiscs($goodsDiscs)
    {
        if ($this->goodsDiscs->contains($goodsDiscs)) {
            $this->goodsDiscs->removeElement($goodsDiscs);
        }

        return $this;
    }
}

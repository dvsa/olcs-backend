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
 *        @ORM\Index(name="fk_licence_vehicle_vehicle1_idx", columns={"vehicle_id"}),
 *        @ORM\Index(name="fk_licence_vehicle_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_licence_vehicle_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_licence_vehicle_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_licence_vehicle_application2_idx", columns={"interim_application_id"}),
 *        @ORM\Index(name="fk_licence_vehicle_licence1", columns={"licence_id"})
 *    }
 * )
 */
class LicenceVehicle implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\ApplicationManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\ReceivedDateField,
        Traits\CustomDeletedDateField,
        Traits\ViAction1Field,
        Traits\SpecifiedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Interim application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", fetch="LAZY")
     * @ORM\JoinColumn(name="interim_application_id", referencedColumnName="id", nullable=true)
     */
    protected $interimApplication;

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
     * Removal date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="removal_date", nullable=true)
     */
    protected $removalDate;

    /**
     * Removal letter seed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="removal_letter_seed_date", nullable=true)
     */
    protected $removalLetterSeedDate;

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
     * Set the interim application
     *
     * @param \Olcs\Db\Entity\Application $interimApplication
     * @return LicenceVehicle
     */
    public function setInterimApplication($interimApplication)
    {
        $this->interimApplication = $interimApplication;

        return $this;
    }

    /**
     * Get the interim application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getInterimApplication()
    {
        return $this->interimApplication;
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
     * Set the removal date
     *
     * @param \DateTime $removalDate
     * @return LicenceVehicle
     */
    public function setRemovalDate($removalDate)
    {
        $this->removalDate = $removalDate;

        return $this;
    }

    /**
     * Get the removal date
     *
     * @return \DateTime
     */
    public function getRemovalDate()
    {
        return $this->removalDate;
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

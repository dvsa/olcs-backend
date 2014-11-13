<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
 *        @ORM\Index(name="fk_licence_vehicle_vehicle1_idx", 
 *            columns={"vehicle_id"}),
 *        @ORM\Index(name="fk_licence_vehicle_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_licence_vehicle_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_licence_vehicle_ref_data1_idx", 
 *            columns={"removal_reason"}),
 *        @ORM\Index(name="fk_licence_vehicle_application1_idx", 
 *            columns={"application_id"}),
 *        @ORM\Index(name="fk_licence_vehicle_licence1", 
 *            columns={"licence_id"})
 *    }
 * )
 */
class LicenceVehicle implements Interfaces\EntityInterface
{

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
     * Removal date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="removal_date", nullable=true)
     */
    protected $removalDate;

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
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Removal reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="removal_reason", referencedColumnName="id", nullable=true)
     */
    protected $removalReason;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", fetch="LAZY")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
     */
    protected $application;

    /**
     * Received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="received_date", nullable=false)
     */
    protected $receivedDate;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Vi action
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vi_action", length=1, nullable=true)
     */
    protected $viAction;

    /**
     * Specified date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="specified_date", nullable=true)
     */
    protected $specifiedDate;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

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

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the last modified by
     *
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the removal reason
     *
     * @param \Olcs\Db\Entity\RefData $removalReason
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setRemovalReason($removalReason)
    {
        $this->removalReason = $removalReason;

        return $this;
    }

    /**
     * Get the removal reason
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRemovalReason()
    {
        return $this->removalReason;
    }

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the received date
     *
     * @param \DateTime $receivedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setReceivedDate($receivedDate)
    {
        $this->receivedDate = $receivedDate;

        return $this;
    }

    /**
     * Get the received date
     *
     * @return \DateTime
     */
    public function getReceivedDate()
    {
        return $this->receivedDate;
    }

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return !is_null($this->deletedDate);
    }

    /**
     * Set the vi action
     *
     * @param string $viAction
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setViAction($viAction)
    {
        $this->viAction = $viAction;

        return $this;
    }

    /**
     * Get the vi action
     *
     * @return string
     */
    public function getViAction()
    {
        return $this->viAction;
    }

    /**
     * Set the specified date
     *
     * @param \DateTime $specifiedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setSpecifiedDate($specifiedDate)
    {
        $this->specifiedDate = $specifiedDate;

        return $this;
    }

    /**
     * Get the specified date
     *
     * @return \DateTime
     */
    public function getSpecifiedDate()
    {
        return $this->specifiedDate;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}

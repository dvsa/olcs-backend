<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Vehicle Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="vehicle",
 *    indexes={
 *        @ORM\Index(name="fk_vehicle_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_vehicle_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_vehicle_ref_data1_idx", 
 *            columns={"psv_type"})
 *    }
 * )
 */
class Vehicle implements Interfaces\EntityInterface
{

    /**
     * Psv type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="psv_type", referencedColumnName="id", nullable=true)
     */
    protected $psvType;

    /**
     * Is novelty
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_novelty", nullable=true)
     */
    protected $isNovelty;

    /**
     * Vrm
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vrm", length=20, nullable=true)
     */
    protected $vrm;

    /**
     * Plated weight
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="plated_weight", nullable=true)
     */
    protected $platedWeight;

    /**
     * Certificate no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="certificate_no", length=50, nullable=true)
     */
    protected $certificateNo;

    /**
     * Section26
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="section_26", nullable=false)
     */
    protected $section26;

    /**
     * Section26 curtail
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="section_26_curtail", nullable=false)
     */
    protected $section26Curtail;

    /**
     * Section26 revoked
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="section_26_revoked", nullable=false)
     */
    protected $section26Revoked;

    /**
     * Section26 suspend
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="section_26_suspend", nullable=false)
     */
    protected $section26Suspend;

    /**
     * Licence vehicle
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\LicenceVehicle", mappedBy="vehicle")
     */
    protected $licenceVehicles;

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
     * Vi action
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vi_action", length=1, nullable=true)
     */
    protected $viAction;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

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
        $this->licenceVehicles = new ArrayCollection();
    }

    /**
     * Set the psv type
     *
     * @param \Olcs\Db\Entity\RefData $psvType
     * @return Vehicle
     */
    public function setPsvType($psvType)
    {
        $this->psvType = $psvType;

        return $this;
    }

    /**
     * Get the psv type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPsvType()
    {
        return $this->psvType;
    }

    /**
     * Set the is novelty
     *
     * @param string $isNovelty
     * @return Vehicle
     */
    public function setIsNovelty($isNovelty)
    {
        $this->isNovelty = $isNovelty;

        return $this;
    }

    /**
     * Get the is novelty
     *
     * @return string
     */
    public function getIsNovelty()
    {
        return $this->isNovelty;
    }

    /**
     * Set the vrm
     *
     * @param string $vrm
     * @return Vehicle
     */
    public function setVrm($vrm)
    {
        $this->vrm = $vrm;

        return $this;
    }

    /**
     * Get the vrm
     *
     * @return string
     */
    public function getVrm()
    {
        return $this->vrm;
    }

    /**
     * Set the plated weight
     *
     * @param int $platedWeight
     * @return Vehicle
     */
    public function setPlatedWeight($platedWeight)
    {
        $this->platedWeight = $platedWeight;

        return $this;
    }

    /**
     * Get the plated weight
     *
     * @return int
     */
    public function getPlatedWeight()
    {
        return $this->platedWeight;
    }

    /**
     * Set the certificate no
     *
     * @param string $certificateNo
     * @return Vehicle
     */
    public function setCertificateNo($certificateNo)
    {
        $this->certificateNo = $certificateNo;

        return $this;
    }

    /**
     * Get the certificate no
     *
     * @return string
     */
    public function getCertificateNo()
    {
        return $this->certificateNo;
    }

    /**
     * Set the section26
     *
     * @param string $section26
     * @return Vehicle
     */
    public function setSection26($section26)
    {
        $this->section26 = $section26;

        return $this;
    }

    /**
     * Get the section26
     *
     * @return string
     */
    public function getSection26()
    {
        return $this->section26;
    }

    /**
     * Set the section26 curtail
     *
     * @param string $section26Curtail
     * @return Vehicle
     */
    public function setSection26Curtail($section26Curtail)
    {
        $this->section26Curtail = $section26Curtail;

        return $this;
    }

    /**
     * Get the section26 curtail
     *
     * @return string
     */
    public function getSection26Curtail()
    {
        return $this->section26Curtail;
    }

    /**
     * Set the section26 revoked
     *
     * @param string $section26Revoked
     * @return Vehicle
     */
    public function setSection26Revoked($section26Revoked)
    {
        $this->section26Revoked = $section26Revoked;

        return $this;
    }

    /**
     * Get the section26 revoked
     *
     * @return string
     */
    public function getSection26Revoked()
    {
        return $this->section26Revoked;
    }

    /**
     * Set the section26 suspend
     *
     * @param string $section26Suspend
     * @return Vehicle
     */
    public function setSection26Suspend($section26Suspend)
    {
        $this->section26Suspend = $section26Suspend;

        return $this;
    }

    /**
     * Get the section26 suspend
     *
     * @return string
     */
    public function getSection26Suspend()
    {
        return $this->section26Suspend;
    }

    /**
     * Set the licence vehicle
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licenceVehicles
     * @return Vehicle
     */
    public function setLicenceVehicles($licenceVehicles)
    {
        $this->licenceVehicles = $licenceVehicles;

        return $this;
    }

    /**
     * Get the licence vehicles
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLicenceVehicles()
    {
        return $this->licenceVehicles;
    }

    /**
     * Add a licence vehicles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licenceVehicles
     * @return Vehicle
     */
    public function addLicenceVehicles($licenceVehicles)
    {
        if ($licenceVehicles instanceof ArrayCollection) {
            $this->licenceVehicles = new ArrayCollection(
                array_merge(
                    $this->licenceVehicles->toArray(),
                    $licenceVehicles->toArray()
                )
            );
        } elseif (!$this->licenceVehicles->contains($licenceVehicles)) {
            $this->licenceVehicles->add($licenceVehicles);
        }

        return $this;
    }

    /**
     * Remove a licence vehicles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licenceVehicles
     * @return Vehicle
     */
    public function removeLicenceVehicles($licenceVehicles)
    {
        if ($this->licenceVehicles->contains($licenceVehicles)) {
            $this->licenceVehicles->removeElement($licenceVehicles);
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

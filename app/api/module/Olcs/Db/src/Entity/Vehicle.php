<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
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
 *        @ORM\Index(name="fk_vehicle_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_vehicle_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_vehicle_ref_data1_idx", columns={"psv_type"})
 *    }
 * )
 */
class Vehicle implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField,
        Traits\ViAction1Field,
        Traits\Vrm20Field;

    /**
     * Certificate no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="certificate_no", length=50, nullable=true)
     */
    protected $certificateNo;

    /**
     * Is novelty
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_novelty", nullable=true)
     */
    protected $isNovelty;

    /**
     * Make model
     *
     * @var string
     *
     * @ORM\Column(type="string", name="make_model", length=100, nullable=true)
     */
    protected $makeModel;

    /**
     * Plated weight
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="plated_weight", nullable=true)
     */
    protected $platedWeight;

    /**
     * Psv type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="psv_type", referencedColumnName="id", nullable=true)
     */
    protected $psvType;

    /**
     * Section26
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_26", nullable=false, options={"default": 0})
     */
    protected $section26 = 0;

    /**
     * Section26 curtail
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_26_curtail", nullable=false, options={"default": 0})
     */
    protected $section26Curtail = 0;

    /**
     * Section26 revoked
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_26_revoked", nullable=false, options={"default": 0})
     */
    protected $section26Revoked = 0;

    /**
     * Section26 suspend
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_26_suspend", nullable=false, options={"default": 0})
     */
    protected $section26Suspend = 0;

    /**
     * Licence vehicle
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\LicenceVehicle", mappedBy="vehicle")
     */
    protected $licenceVehicles;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->licenceVehicles = new ArrayCollection();
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
     * Set the make model
     *
     * @param string $makeModel
     * @return Vehicle
     */
    public function setMakeModel($makeModel)
    {
        $this->makeModel = $makeModel;

        return $this;
    }

    /**
     * Get the make model
     *
     * @return string
     */
    public function getMakeModel()
    {
        return $this->makeModel;
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
     * Set the section26
     *
     * @param boolean $section26
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
     * @return boolean
     */
    public function getSection26()
    {
        return $this->section26;
    }

    /**
     * Set the section26 curtail
     *
     * @param boolean $section26Curtail
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
     * @return boolean
     */
    public function getSection26Curtail()
    {
        return $this->section26Curtail;
    }

    /**
     * Set the section26 revoked
     *
     * @param boolean $section26Revoked
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
     * @return boolean
     */
    public function getSection26Revoked()
    {
        return $this->section26Revoked;
    }

    /**
     * Set the section26 suspend
     *
     * @param boolean $section26Suspend
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
     * @return boolean
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
}

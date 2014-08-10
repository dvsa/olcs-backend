<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
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
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Vrm20Field,
        Traits\ViAction1Field,
        Traits\CustomDeletedDateField,
        Traits\SpecifiedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Psv type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="psv_type", referencedColumnName="id")
     */
    protected $psvType;

    /**
     * Plated weight
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="plated_weight", nullable=true)
     */
    protected $platedWeight;

    /**
     * Is articulated
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="is_articulated", nullable=true)
     */
    protected $isArticulated;

    /**
     * Certificate no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="certificate_no", length=50, nullable=true)
     */
    protected $certificateNo;

    /**
     * Is refrigerated
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="is_refrigerated", nullable=true)
     */
    protected $isRefrigerated;

    /**
     * Is tipper
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="is_tipper", nullable=true)
     */
    protected $isTipper;

    /**
     * Section26
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_26", nullable=false)
     */
    protected $section26;

    /**
     * Section26 curtail
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_26_curtail", nullable=false)
     */
    protected $section26Curtail;

    /**
     * Section26 revoked
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_26_revoked", nullable=false)
     */
    protected $section26Revoked;

    /**
     * Section26 suspend
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_26_suspend", nullable=false)
     */
    protected $section26Suspend;

    /**
     * Make model
     *
     * @var string
     *
     * @ORM\Column(type="string", name="make_model", length=100, nullable=true)
     */
    protected $makeModel;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
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
     * Set the is articulated
     *
     * @param unknown $isArticulated
     * @return Vehicle
     */
    public function setIsArticulated($isArticulated)
    {
        $this->isArticulated = $isArticulated;

        return $this;
    }

    /**
     * Get the is articulated
     *
     * @return unknown
     */
    public function getIsArticulated()
    {
        return $this->isArticulated;
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
     * Set the is refrigerated
     *
     * @param unknown $isRefrigerated
     * @return Vehicle
     */
    public function setIsRefrigerated($isRefrigerated)
    {
        $this->isRefrigerated = $isRefrigerated;

        return $this;
    }

    /**
     * Get the is refrigerated
     *
     * @return unknown
     */
    public function getIsRefrigerated()
    {
        return $this->isRefrigerated;
    }


    /**
     * Set the is tipper
     *
     * @param unknown $isTipper
     * @return Vehicle
     */
    public function setIsTipper($isTipper)
    {
        $this->isTipper = $isTipper;

        return $this;
    }

    /**
     * Get the is tipper
     *
     * @return unknown
     */
    public function getIsTipper()
    {
        return $this->isTipper;
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

}

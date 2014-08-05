<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Licence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="licence",
 *    indexes={
 *        @ORM\Index(name="fk_licence_vehicle_inspectorate1_idx", columns={"enforcement_area_id"}),
 *        @ORM\Index(name="fk_licence_traffic_area1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_licence_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_licence_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_licence_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_licence_ref_data1_idx", columns={"goods_or_psv"}),
 *        @ORM\Index(name="fk_licence_ref_data2_idx", columns={"licence_type"}),
 *        @ORM\Index(name="fk_licence_ref_data3_idx", columns={"status"}),
 *        @ORM\Index(name="fk_licence_ref_data4_idx", columns={"tachograph_ins"})
 *    }
 * )
 */
class Licence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LicenceTypeManyToOne,
        Traits\StatusManyToOne,
        Traits\GoodsOrPsvManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\OrganisationManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicNo18Field,
        Traits\ViAction1Field,
        Traits\TotAuthTrailersField,
        Traits\TotAuthVehiclesField,
        Traits\TotAuthSmallVehiclesField,
        Traits\TotAuthMediumVehiclesField,
        Traits\TotAuthLargeVehiclesField,
        Traits\TotCommunityLicencesField,
        Traits\ExpiryDateField,
        Traits\InForceDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Tachograph ins
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="tachograph_ins", referencedColumnName="id")
     */
    protected $tachographIns;

    /**
     * Enforcement area
     *
     * @var \Olcs\Db\Entity\EnforcementArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\EnforcementArea")
     * @ORM\JoinColumn(name="enforcement_area_id", referencedColumnName="id")
     */
    protected $enforcementArea;

    /**
     * Trailers in possession
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="trailers_in_possession", nullable=true)
     */
    protected $trailersInPossession;

    /**
     * Fabs reference
     *
     * @var string
     *
     * @ORM\Column(type="string", name="fabs_reference", length=10, nullable=true)
     */
    protected $fabsReference;

    /**
     * Granted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="granted_date", nullable=true)
     */
    protected $grantedDate;

    /**
     * Review date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="review_date", nullable=true)
     */
    protected $reviewDate;

    /**
     * Fee date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="fee_date", nullable=true)
     */
    protected $feeDate;

    /**
     * Surrendered date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="surrendered_date", nullable=true)
     */
    protected $surrenderedDate;

    /**
     * Safety ins trailers
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="safety_ins_trailers", nullable=true)
     */
    protected $safetyInsTrailers;

    /**
     * Safety ins vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="safety_ins_vehicles", nullable=true)
     */
    protected $safetyInsVehicles;

    /**
     * Safety ins
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="safety_ins", nullable=false)
     */
    protected $safetyIns = 0;

    /**
     * Safety ins varies
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="safety_ins_varies", nullable=false)
     */
    protected $safetyInsVaries = 0;

    /**
     * Ni flag
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="ni_flag", nullable=false)
     */
    protected $niFlag = 0;

    /**
     * Tachograph ins name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="tachograph_ins_name", length=90, nullable=true)
     */
    protected $tachographInsName;

    /**
     * Psv discs to be printed no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="psv_discs_to_be_printed_no", nullable=true)
     */
    protected $psvDiscsToBePrintedNo;

    /**
     * Translate to welsh
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="translate_to_welsh", nullable=false)
     */
    protected $translateToWelsh = 0;

    /**
     * Set the tachograph ins
     *
     * @param \Olcs\Db\Entity\RefData $tachographIns
     * @return \Olcs\Db\Entity\Licence
     */
    public function setTachographIns($tachographIns)
    {
        $this->tachographIns = $tachographIns;

        return $this;
    }

    /**
     * Get the tachograph ins
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getTachographIns()
    {
        return $this->tachographIns;
    }

    /**
     * Set the enforcement area
     *
     * @param \Olcs\Db\Entity\EnforcementArea $enforcementArea
     * @return \Olcs\Db\Entity\Licence
     */
    public function setEnforcementArea($enforcementArea)
    {
        $this->enforcementArea = $enforcementArea;

        return $this;
    }

    /**
     * Get the enforcement area
     *
     * @return \Olcs\Db\Entity\EnforcementArea
     */
    public function getEnforcementArea()
    {
        return $this->enforcementArea;
    }

    /**
     * Set the trailers in possession
     *
     * @param int $trailersInPossession
     * @return \Olcs\Db\Entity\Licence
     */
    public function setTrailersInPossession($trailersInPossession)
    {
        $this->trailersInPossession = $trailersInPossession;

        return $this;
    }

    /**
     * Get the trailers in possession
     *
     * @return int
     */
    public function getTrailersInPossession()
    {
        return $this->trailersInPossession;
    }

    /**
     * Set the fabs reference
     *
     * @param string $fabsReference
     * @return \Olcs\Db\Entity\Licence
     */
    public function setFabsReference($fabsReference)
    {
        $this->fabsReference = $fabsReference;

        return $this;
    }

    /**
     * Get the fabs reference
     *
     * @return string
     */
    public function getFabsReference()
    {
        return $this->fabsReference;
    }

    /**
     * Set the granted date
     *
     * @param \DateTime $grantedDate
     * @return \Olcs\Db\Entity\Licence
     */
    public function setGrantedDate($grantedDate)
    {
        $this->grantedDate = $grantedDate;

        return $this;
    }

    /**
     * Get the granted date
     *
     * @return \DateTime
     */
    public function getGrantedDate()
    {
        return $this->grantedDate;
    }

    /**
     * Set the review date
     *
     * @param \DateTime $reviewDate
     * @return \Olcs\Db\Entity\Licence
     */
    public function setReviewDate($reviewDate)
    {
        $this->reviewDate = $reviewDate;

        return $this;
    }

    /**
     * Get the review date
     *
     * @return \DateTime
     */
    public function getReviewDate()
    {
        return $this->reviewDate;
    }

    /**
     * Set the fee date
     *
     * @param \DateTime $feeDate
     * @return \Olcs\Db\Entity\Licence
     */
    public function setFeeDate($feeDate)
    {
        $this->feeDate = $feeDate;

        return $this;
    }

    /**
     * Get the fee date
     *
     * @return \DateTime
     */
    public function getFeeDate()
    {
        return $this->feeDate;
    }

    /**
     * Set the surrendered date
     *
     * @param \DateTime $surrenderedDate
     * @return \Olcs\Db\Entity\Licence
     */
    public function setSurrenderedDate($surrenderedDate)
    {
        $this->surrenderedDate = $surrenderedDate;

        return $this;
    }

    /**
     * Get the surrendered date
     *
     * @return \DateTime
     */
    public function getSurrenderedDate()
    {
        return $this->surrenderedDate;
    }

    /**
     * Set the safety ins trailers
     *
     * @param int $safetyInsTrailers
     * @return \Olcs\Db\Entity\Licence
     */
    public function setSafetyInsTrailers($safetyInsTrailers)
    {
        $this->safetyInsTrailers = $safetyInsTrailers;

        return $this;
    }

    /**
     * Get the safety ins trailers
     *
     * @return int
     */
    public function getSafetyInsTrailers()
    {
        return $this->safetyInsTrailers;
    }

    /**
     * Set the safety ins vehicles
     *
     * @param int $safetyInsVehicles
     * @return \Olcs\Db\Entity\Licence
     */
    public function setSafetyInsVehicles($safetyInsVehicles)
    {
        $this->safetyInsVehicles = $safetyInsVehicles;

        return $this;
    }

    /**
     * Get the safety ins vehicles
     *
     * @return int
     */
    public function getSafetyInsVehicles()
    {
        return $this->safetyInsVehicles;
    }

    /**
     * Set the safety ins
     *
     * @param boolean $safetyIns
     * @return \Olcs\Db\Entity\Licence
     */
    public function setSafetyIns($safetyIns)
    {
        $this->safetyIns = $safetyIns;

        return $this;
    }

    /**
     * Get the safety ins
     *
     * @return boolean
     */
    public function getSafetyIns()
    {
        return $this->safetyIns;
    }

    /**
     * Set the safety ins varies
     *
     * @param boolean $safetyInsVaries
     * @return \Olcs\Db\Entity\Licence
     */
    public function setSafetyInsVaries($safetyInsVaries)
    {
        $this->safetyInsVaries = $safetyInsVaries;

        return $this;
    }

    /**
     * Get the safety ins varies
     *
     * @return boolean
     */
    public function getSafetyInsVaries()
    {
        return $this->safetyInsVaries;
    }

    /**
     * Set the ni flag
     *
     * @param boolean $niFlag
     * @return \Olcs\Db\Entity\Licence
     */
    public function setNiFlag($niFlag)
    {
        $this->niFlag = $niFlag;

        return $this;
    }

    /**
     * Get the ni flag
     *
     * @return boolean
     */
    public function getNiFlag()
    {
        return $this->niFlag;
    }

    /**
     * Set the tachograph ins name
     *
     * @param string $tachographInsName
     * @return \Olcs\Db\Entity\Licence
     */
    public function setTachographInsName($tachographInsName)
    {
        $this->tachographInsName = $tachographInsName;

        return $this;
    }

    /**
     * Get the tachograph ins name
     *
     * @return string
     */
    public function getTachographInsName()
    {
        return $this->tachographInsName;
    }

    /**
     * Set the psv discs to be printed no
     *
     * @param int $psvDiscsToBePrintedNo
     * @return \Olcs\Db\Entity\Licence
     */
    public function setPsvDiscsToBePrintedNo($psvDiscsToBePrintedNo)
    {
        $this->psvDiscsToBePrintedNo = $psvDiscsToBePrintedNo;

        return $this;
    }

    /**
     * Get the psv discs to be printed no
     *
     * @return int
     */
    public function getPsvDiscsToBePrintedNo()
    {
        return $this->psvDiscsToBePrintedNo;
    }

    /**
     * Set the translate to welsh
     *
     * @param boolean $translateToWelsh
     * @return \Olcs\Db\Entity\Licence
     */
    public function setTranslateToWelsh($translateToWelsh)
    {
        $this->translateToWelsh = $translateToWelsh;

        return $this;
    }

    /**
     * Get the translate to welsh
     *
     * @return boolean
     */
    public function getTranslateToWelsh()
    {
        return $this->translateToWelsh;
    }
}

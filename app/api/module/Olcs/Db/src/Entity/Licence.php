<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Licence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
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
 *        @ORM\Index(name="fk_licence_ref_data4_idx", columns={"tachograph_ins"}),
 *        @ORM\Index(name="fk_licence_contact_details1_idx", columns={"correspondence_cd_id"}),
 *        @ORM\Index(name="fk_licence_contact_details2_idx", columns={"establishment_cd_id"}),
 *        @ORM\Index(name="fk_licence_contact_details3_idx", columns={"transport_consultant_cd_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="licence_lic_no_idx", columns={"lic_no"})
 *    }
 * )
 */
class Licence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\ExpiryDateField,
        Traits\GoodsOrPsvManyToOne,
        Traits\GrantedDateField,
        Traits\IdIdentity,
        Traits\InForceDateField,
        Traits\IsMaintenanceSuitableField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicNo18Field,
        Traits\LicenceTypeManyToOne,
        Traits\NiFlagField,
        Traits\StatusManyToOne,
        Traits\TotAuthLargeVehiclesField,
        Traits\TotAuthMediumVehiclesField,
        Traits\TotAuthSmallVehiclesField,
        Traits\TotAuthTrailersField,
        Traits\TotAuthVehiclesField,
        Traits\TotCommunityLicencesField,
        Traits\TrafficAreaManyToOneAlt1,
        Traits\CustomVersionField,
        Traits\ViAction1Field;

    /**
     * Company subsidiary
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\CompanySubsidiary", mappedBy="licences")
     */
    protected $companySubsidiarys;

    /**
     * Correspondence cd
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="correspondence_cd_id", referencedColumnName="id", nullable=true)
     */
    protected $correspondenceCd;

    /**
     * Enforcement area
     *
     * @var \Olcs\Db\Entity\EnforcementArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\EnforcementArea")
     * @ORM\JoinColumn(name="enforcement_area_id", referencedColumnName="id", nullable=true)
     */
    protected $enforcementArea;

    /**
     * Establishment cd
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="establishment_cd_id", referencedColumnName="id", nullable=true)
     */
    protected $establishmentCd;

    /**
     * Fabs reference
     *
     * @var string
     *
     * @ORM\Column(type="string", name="fabs_reference", length=10, nullable=true)
     */
    protected $fabsReference;

    /**
     * Fee date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="fee_date", nullable=true)
     */
    protected $feeDate;

    /**
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", inversedBy="licences")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

    /**
     * Psv discs to be printed no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="psv_discs_to_be_printed_no", nullable=true)
     */
    protected $psvDiscsToBePrintedNo;

    /**
     * Review date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="review_date", nullable=true)
     */
    protected $reviewDate;

    /**
     * Safety ins
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="safety_ins", nullable=false, options={"default": 0})
     */
    protected $safetyIns = 0;

    /**
     * Safety ins trailers
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="safety_ins_trailers", nullable=true)
     */
    protected $safetyInsTrailers;

    /**
     * Safety ins varies
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="safety_ins_varies", nullable=true)
     */
    protected $safetyInsVaries;

    /**
     * Safety ins vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="safety_ins_vehicles", nullable=true)
     */
    protected $safetyInsVehicles;

    /**
     * Surrendered date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="surrendered_date", nullable=true)
     */
    protected $surrenderedDate;

    /**
     * Tachograph ins
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="tachograph_ins", referencedColumnName="id", nullable=true)
     */
    protected $tachographIns;

    /**
     * Tachograph ins name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="tachograph_ins_name", length=90, nullable=true)
     */
    protected $tachographInsName;

    /**
     * Trailers in possession
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="trailers_in_possession", nullable=true)
     */
    protected $trailersInPossession;

    /**
     * Translate to welsh
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="translate_to_welsh", nullable=false, options={"default": 0})
     */
    protected $translateToWelsh = 0;

    /**
     * Transport consultant cd
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="transport_consultant_cd_id", referencedColumnName="id", nullable=true)
     */
    protected $transportConsultantCd;

    /**
     * Application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Application", mappedBy="licence")
     */
    protected $applications;

    /**
     * Case
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Cases", mappedBy="licence")
     */
    protected $cases;

    /**
     * Community lic
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\CommunityLic", mappedBy="licence")
     */
    protected $communityLics;

    /**
     * Condition undertaking
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ConditionUndertaking", mappedBy="licence")
     */
    protected $conditionUndertakings;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="licence")
     */
    protected $documents;

    /**
     * Operating centre
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\LicenceOperatingCentre", mappedBy="licence")
     */
    protected $operatingCentres;

    /**
     * Licence vehicle
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\LicenceVehicle", mappedBy="licence")
     */
    protected $licenceVehicles;

    /**
     * Private hire licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PrivateHireLicence", mappedBy="licence")
     */
    protected $privateHireLicences;

    /**
     * Psv disc
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PsvDisc", mappedBy="licence")
     * @ORM\OrderBy({"discNo" = "ASC"})
     */
    protected $psvDiscs;

    /**
     * Publication link
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PublicationLink", mappedBy="licence")
     */
    protected $publicationLinks;

    /**
     * Tm licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\TransportManagerLicence", mappedBy="licence")
     */
    protected $tmLicences;

    /**
     * Workshop
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Workshop", mappedBy="licence")
     */
    protected $workshops;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->companySubsidiarys = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->cases = new ArrayCollection();
        $this->communityLics = new ArrayCollection();
        $this->conditionUndertakings = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->operatingCentres = new ArrayCollection();
        $this->licenceVehicles = new ArrayCollection();
        $this->privateHireLicences = new ArrayCollection();
        $this->psvDiscs = new ArrayCollection();
        $this->publicationLinks = new ArrayCollection();
        $this->tmLicences = new ArrayCollection();
        $this->workshops = new ArrayCollection();
    }

    /**
     * Set the company subsidiary
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $companySubsidiarys
     * @return Licence
     */
    public function setCompanySubsidiarys($companySubsidiarys)
    {
        $this->companySubsidiarys = $companySubsidiarys;

        return $this;
    }

    /**
     * Get the company subsidiarys
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCompanySubsidiarys()
    {
        return $this->companySubsidiarys;
    }

    /**
     * Add a company subsidiarys
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $companySubsidiarys
     * @return Licence
     */
    public function addCompanySubsidiarys($companySubsidiarys)
    {
        if ($companySubsidiarys instanceof ArrayCollection) {
            $this->companySubsidiarys = new ArrayCollection(
                array_merge(
                    $this->companySubsidiarys->toArray(),
                    $companySubsidiarys->toArray()
                )
            );
        } elseif (!$this->companySubsidiarys->contains($companySubsidiarys)) {
            $this->companySubsidiarys->add($companySubsidiarys);
        }

        return $this;
    }

    /**
     * Remove a company subsidiarys
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $companySubsidiarys
     * @return Licence
     */
    public function removeCompanySubsidiarys($companySubsidiarys)
    {
        if ($this->companySubsidiarys->contains($companySubsidiarys)) {
            $this->companySubsidiarys->removeElement($companySubsidiarys);
        }

        return $this;
    }

    /**
     * Set the correspondence cd
     *
     * @param \Olcs\Db\Entity\ContactDetails $correspondenceCd
     * @return Licence
     */
    public function setCorrespondenceCd($correspondenceCd)
    {
        $this->correspondenceCd = $correspondenceCd;

        return $this;
    }

    /**
     * Get the correspondence cd
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getCorrespondenceCd()
    {
        return $this->correspondenceCd;
    }

    /**
     * Set the enforcement area
     *
     * @param \Olcs\Db\Entity\EnforcementArea $enforcementArea
     * @return Licence
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
     * Set the establishment cd
     *
     * @param \Olcs\Db\Entity\ContactDetails $establishmentCd
     * @return Licence
     */
    public function setEstablishmentCd($establishmentCd)
    {
        $this->establishmentCd = $establishmentCd;

        return $this;
    }

    /**
     * Get the establishment cd
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getEstablishmentCd()
    {
        return $this->establishmentCd;
    }

    /**
     * Set the fabs reference
     *
     * @param string $fabsReference
     * @return Licence
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
     * Set the fee date
     *
     * @param \DateTime $feeDate
     * @return Licence
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
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return Licence
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get the organisation
     *
     * @return \Olcs\Db\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set the psv discs to be printed no
     *
     * @param int $psvDiscsToBePrintedNo
     * @return Licence
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
     * Set the review date
     *
     * @param \DateTime $reviewDate
     * @return Licence
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
     * Set the safety ins
     *
     * @param string $safetyIns
     * @return Licence
     */
    public function setSafetyIns($safetyIns)
    {
        $this->safetyIns = $safetyIns;

        return $this;
    }

    /**
     * Get the safety ins
     *
     * @return string
     */
    public function getSafetyIns()
    {
        return $this->safetyIns;
    }

    /**
     * Set the safety ins trailers
     *
     * @param int $safetyInsTrailers
     * @return Licence
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
     * Set the safety ins varies
     *
     * @param string $safetyInsVaries
     * @return Licence
     */
    public function setSafetyInsVaries($safetyInsVaries)
    {
        $this->safetyInsVaries = $safetyInsVaries;

        return $this;
    }

    /**
     * Get the safety ins varies
     *
     * @return string
     */
    public function getSafetyInsVaries()
    {
        return $this->safetyInsVaries;
    }

    /**
     * Set the safety ins vehicles
     *
     * @param int $safetyInsVehicles
     * @return Licence
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
     * Set the surrendered date
     *
     * @param \DateTime $surrenderedDate
     * @return Licence
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
     * Set the tachograph ins
     *
     * @param \Olcs\Db\Entity\RefData $tachographIns
     * @return Licence
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
     * Set the tachograph ins name
     *
     * @param string $tachographInsName
     * @return Licence
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
     * Set the trailers in possession
     *
     * @param int $trailersInPossession
     * @return Licence
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
     * Set the translate to welsh
     *
     * @param string $translateToWelsh
     * @return Licence
     */
    public function setTranslateToWelsh($translateToWelsh)
    {
        $this->translateToWelsh = $translateToWelsh;

        return $this;
    }

    /**
     * Get the translate to welsh
     *
     * @return string
     */
    public function getTranslateToWelsh()
    {
        return $this->translateToWelsh;
    }

    /**
     * Set the transport consultant cd
     *
     * @param \Olcs\Db\Entity\ContactDetails $transportConsultantCd
     * @return Licence
     */
    public function setTransportConsultantCd($transportConsultantCd)
    {
        $this->transportConsultantCd = $transportConsultantCd;

        return $this;
    }

    /**
     * Get the transport consultant cd
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getTransportConsultantCd()
    {
        return $this->transportConsultantCd;
    }

    /**
     * Set the application
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applications
     * @return Licence
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;

        return $this;
    }

    /**
     * Get the applications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Add a applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applications
     * @return Licence
     */
    public function addApplications($applications)
    {
        if ($applications instanceof ArrayCollection) {
            $this->applications = new ArrayCollection(
                array_merge(
                    $this->applications->toArray(),
                    $applications->toArray()
                )
            );
        } elseif (!$this->applications->contains($applications)) {
            $this->applications->add($applications);
        }

        return $this;
    }

    /**
     * Remove a applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applications
     * @return Licence
     */
    public function removeApplications($applications)
    {
        if ($this->applications->contains($applications)) {
            $this->applications->removeElement($applications);
        }

        return $this;
    }

    /**
     * Set the case
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return Licence
     */
    public function setCases($cases)
    {
        $this->cases = $cases;

        return $this;
    }

    /**
     * Get the cases
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCases()
    {
        return $this->cases;
    }

    /**
     * Add a cases
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return Licence
     */
    public function addCases($cases)
    {
        if ($cases instanceof ArrayCollection) {
            $this->cases = new ArrayCollection(
                array_merge(
                    $this->cases->toArray(),
                    $cases->toArray()
                )
            );
        } elseif (!$this->cases->contains($cases)) {
            $this->cases->add($cases);
        }

        return $this;
    }

    /**
     * Remove a cases
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return Licence
     */
    public function removeCases($cases)
    {
        if ($this->cases->contains($cases)) {
            $this->cases->removeElement($cases);
        }

        return $this;
    }

    /**
     * Set the community lic
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $communityLics
     * @return Licence
     */
    public function setCommunityLics($communityLics)
    {
        $this->communityLics = $communityLics;

        return $this;
    }

    /**
     * Get the community lics
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCommunityLics()
    {
        return $this->communityLics;
    }

    /**
     * Add a community lics
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $communityLics
     * @return Licence
     */
    public function addCommunityLics($communityLics)
    {
        if ($communityLics instanceof ArrayCollection) {
            $this->communityLics = new ArrayCollection(
                array_merge(
                    $this->communityLics->toArray(),
                    $communityLics->toArray()
                )
            );
        } elseif (!$this->communityLics->contains($communityLics)) {
            $this->communityLics->add($communityLics);
        }

        return $this;
    }

    /**
     * Remove a community lics
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $communityLics
     * @return Licence
     */
    public function removeCommunityLics($communityLics)
    {
        if ($this->communityLics->contains($communityLics)) {
            $this->communityLics->removeElement($communityLics);
        }

        return $this;
    }

    /**
     * Set the condition undertaking
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
     * @return Licence
     */
    public function setConditionUndertakings($conditionUndertakings)
    {
        $this->conditionUndertakings = $conditionUndertakings;

        return $this;
    }

    /**
     * Get the condition undertakings
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getConditionUndertakings()
    {
        return $this->conditionUndertakings;
    }

    /**
     * Add a condition undertakings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
     * @return Licence
     */
    public function addConditionUndertakings($conditionUndertakings)
    {
        if ($conditionUndertakings instanceof ArrayCollection) {
            $this->conditionUndertakings = new ArrayCollection(
                array_merge(
                    $this->conditionUndertakings->toArray(),
                    $conditionUndertakings->toArray()
                )
            );
        } elseif (!$this->conditionUndertakings->contains($conditionUndertakings)) {
            $this->conditionUndertakings->add($conditionUndertakings);
        }

        return $this;
    }

    /**
     * Remove a condition undertakings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
     * @return Licence
     */
    public function removeConditionUndertakings($conditionUndertakings)
    {
        if ($this->conditionUndertakings->contains($conditionUndertakings)) {
            $this->conditionUndertakings->removeElement($conditionUndertakings);
        }

        return $this;
    }

    /**
     * Set the document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Licence
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;

        return $this;
    }

    /**
     * Get the documents
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add a documents
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Licence
     */
    public function addDocuments($documents)
    {
        if ($documents instanceof ArrayCollection) {
            $this->documents = new ArrayCollection(
                array_merge(
                    $this->documents->toArray(),
                    $documents->toArray()
                )
            );
        } elseif (!$this->documents->contains($documents)) {
            $this->documents->add($documents);
        }

        return $this;
    }

    /**
     * Remove a documents
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Licence
     */
    public function removeDocuments($documents)
    {
        if ($this->documents->contains($documents)) {
            $this->documents->removeElement($documents);
        }

        return $this;
    }

    /**
     * Set the operating centre
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return Licence
     */
    public function setOperatingCentres($operatingCentres)
    {
        $this->operatingCentres = $operatingCentres;

        return $this;
    }

    /**
     * Get the operating centres
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOperatingCentres()
    {
        return $this->operatingCentres;
    }

    /**
     * Add a operating centres
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return Licence
     */
    public function addOperatingCentres($operatingCentres)
    {
        if ($operatingCentres instanceof ArrayCollection) {
            $this->operatingCentres = new ArrayCollection(
                array_merge(
                    $this->operatingCentres->toArray(),
                    $operatingCentres->toArray()
                )
            );
        } elseif (!$this->operatingCentres->contains($operatingCentres)) {
            $this->operatingCentres->add($operatingCentres);
        }

        return $this;
    }

    /**
     * Remove a operating centres
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return Licence
     */
    public function removeOperatingCentres($operatingCentres)
    {
        if ($this->operatingCentres->contains($operatingCentres)) {
            $this->operatingCentres->removeElement($operatingCentres);
        }

        return $this;
    }

    /**
     * Set the licence vehicle
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licenceVehicles
     * @return Licence
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
     * @return Licence
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
     * @return Licence
     */
    public function removeLicenceVehicles($licenceVehicles)
    {
        if ($this->licenceVehicles->contains($licenceVehicles)) {
            $this->licenceVehicles->removeElement($licenceVehicles);
        }

        return $this;
    }

    /**
     * Set the private hire licence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $privateHireLicences
     * @return Licence
     */
    public function setPrivateHireLicences($privateHireLicences)
    {
        $this->privateHireLicences = $privateHireLicences;

        return $this;
    }

    /**
     * Get the private hire licences
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPrivateHireLicences()
    {
        return $this->privateHireLicences;
    }

    /**
     * Add a private hire licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $privateHireLicences
     * @return Licence
     */
    public function addPrivateHireLicences($privateHireLicences)
    {
        if ($privateHireLicences instanceof ArrayCollection) {
            $this->privateHireLicences = new ArrayCollection(
                array_merge(
                    $this->privateHireLicences->toArray(),
                    $privateHireLicences->toArray()
                )
            );
        } elseif (!$this->privateHireLicences->contains($privateHireLicences)) {
            $this->privateHireLicences->add($privateHireLicences);
        }

        return $this;
    }

    /**
     * Remove a private hire licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $privateHireLicences
     * @return Licence
     */
    public function removePrivateHireLicences($privateHireLicences)
    {
        if ($this->privateHireLicences->contains($privateHireLicences)) {
            $this->privateHireLicences->removeElement($privateHireLicences);
        }

        return $this;
    }

    /**
     * Set the psv disc
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $psvDiscs
     * @return Licence
     */
    public function setPsvDiscs($psvDiscs)
    {
        $this->psvDiscs = $psvDiscs;

        return $this;
    }

    /**
     * Get the psv discs
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPsvDiscs()
    {
        return $this->psvDiscs;
    }

    /**
     * Add a psv discs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $psvDiscs
     * @return Licence
     */
    public function addPsvDiscs($psvDiscs)
    {
        if ($psvDiscs instanceof ArrayCollection) {
            $this->psvDiscs = new ArrayCollection(
                array_merge(
                    $this->psvDiscs->toArray(),
                    $psvDiscs->toArray()
                )
            );
        } elseif (!$this->psvDiscs->contains($psvDiscs)) {
            $this->psvDiscs->add($psvDiscs);
        }

        return $this;
    }

    /**
     * Remove a psv discs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $psvDiscs
     * @return Licence
     */
    public function removePsvDiscs($psvDiscs)
    {
        if ($this->psvDiscs->contains($psvDiscs)) {
            $this->psvDiscs->removeElement($psvDiscs);
        }

        return $this;
    }

    /**
     * Set the publication link
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Licence
     */
    public function setPublicationLinks($publicationLinks)
    {
        $this->publicationLinks = $publicationLinks;

        return $this;
    }

    /**
     * Get the publication links
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPublicationLinks()
    {
        return $this->publicationLinks;
    }

    /**
     * Add a publication links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Licence
     */
    public function addPublicationLinks($publicationLinks)
    {
        if ($publicationLinks instanceof ArrayCollection) {
            $this->publicationLinks = new ArrayCollection(
                array_merge(
                    $this->publicationLinks->toArray(),
                    $publicationLinks->toArray()
                )
            );
        } elseif (!$this->publicationLinks->contains($publicationLinks)) {
            $this->publicationLinks->add($publicationLinks);
        }

        return $this;
    }

    /**
     * Remove a publication links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Licence
     */
    public function removePublicationLinks($publicationLinks)
    {
        if ($this->publicationLinks->contains($publicationLinks)) {
            $this->publicationLinks->removeElement($publicationLinks);
        }

        return $this;
    }

    /**
     * Set the tm licence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmLicences
     * @return Licence
     */
    public function setTmLicences($tmLicences)
    {
        $this->tmLicences = $tmLicences;

        return $this;
    }

    /**
     * Get the tm licences
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmLicences()
    {
        return $this->tmLicences;
    }

    /**
     * Add a tm licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmLicences
     * @return Licence
     */
    public function addTmLicences($tmLicences)
    {
        if ($tmLicences instanceof ArrayCollection) {
            $this->tmLicences = new ArrayCollection(
                array_merge(
                    $this->tmLicences->toArray(),
                    $tmLicences->toArray()
                )
            );
        } elseif (!$this->tmLicences->contains($tmLicences)) {
            $this->tmLicences->add($tmLicences);
        }

        return $this;
    }

    /**
     * Remove a tm licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmLicences
     * @return Licence
     */
    public function removeTmLicences($tmLicences)
    {
        if ($this->tmLicences->contains($tmLicences)) {
            $this->tmLicences->removeElement($tmLicences);
        }

        return $this;
    }

    /**
     * Set the workshop
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $workshops
     * @return Licence
     */
    public function setWorkshops($workshops)
    {
        $this->workshops = $workshops;

        return $this;
    }

    /**
     * Get the workshops
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getWorkshops()
    {
        return $this->workshops;
    }

    /**
     * Add a workshops
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $workshops
     * @return Licence
     */
    public function addWorkshops($workshops)
    {
        if ($workshops instanceof ArrayCollection) {
            $this->workshops = new ArrayCollection(
                array_merge(
                    $this->workshops->toArray(),
                    $workshops->toArray()
                )
            );
        } elseif (!$this->workshops->contains($workshops)) {
            $this->workshops->add($workshops);
        }

        return $this;
    }

    /**
     * Remove a workshops
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $workshops
     * @return Licence
     */
    public function removeWorkshops($workshops)
    {
        if ($this->workshops->contains($workshops)) {
            $this->workshops->removeElement($workshops);
        }

        return $this;
    }
}

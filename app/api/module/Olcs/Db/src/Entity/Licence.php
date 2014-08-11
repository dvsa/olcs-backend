<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
        Traits\CreatedByManyToOne,
        Traits\EnforcementAreaManyToOne,
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
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", inversedBy="licences")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
     */
    protected $organisation;

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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="safety_ins", nullable=false)
     */
    protected $safetyIns = 0;

    /**
     * Safety ins varies
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="safety_ins_varies", nullable=false)
     */
    protected $safetyInsVaries = 0;

    /**
     * Ni flag
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="ni_flag", nullable=false)
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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="translate_to_welsh", nullable=false)
     */
    protected $translateToWelsh = 0;

    /**
     * Application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Application", mappedBy="licence")
     */
    protected $applications;

    /**
     * Contact detail
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ContactDetails", mappedBy="licence")
     */
    protected $contactDetails;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="licence")
     */
    protected $documents;

    /**
     * Licence vehicle
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\LicenceVehicle", mappedBy="licence")
     */
    protected $licenceVehicles;

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
        $this->applications = new ArrayCollection();
        $this->contactDetails = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->licenceVehicles = new ArrayCollection();
        $this->workshops = new ArrayCollection();
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
     * Set the granted date
     *
     * @param \DateTime $grantedDate
     * @return Licence
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
     * Set the safety ins
     *
     * @param unknown $safetyIns
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
     * @return unknown
     */
    public function getSafetyIns()
    {
        return $this->safetyIns;
    }


    /**
     * Set the safety ins varies
     *
     * @param unknown $safetyInsVaries
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
     * @return unknown
     */
    public function getSafetyInsVaries()
    {
        return $this->safetyInsVaries;
    }


    /**
     * Set the ni flag
     *
     * @param unknown $niFlag
     * @return Licence
     */
    public function setNiFlag($niFlag)
    {
        $this->niFlag = $niFlag;

        return $this;
    }

    /**
     * Get the ni flag
     *
     * @return unknown
     */
    public function getNiFlag()
    {
        return $this->niFlag;
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
     * Set the translate to welsh
     *
     * @param unknown $translateToWelsh
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
     * @return unknown
     */
    public function getTranslateToWelsh()
    {
        return $this->translateToWelsh;
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
            $this->applications->remove($applications);
        }

        return $this;
    }


    /**
     * Set the contact detail
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $contactDetails
     * @return Licence
     */
    public function setContactDetails($contactDetails)
    {
        $this->contactDetails = $contactDetails;

        return $this;
    }

    /**
     * Get the contact details
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getContactDetails()
    {
        return $this->contactDetails;
    }

    /**
     * Add a contact details
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $contactDetails
     * @return Licence
     */
    public function addContactDetails($contactDetails)
    {
        if ($contactDetails instanceof ArrayCollection) {
            $this->contactDetails = new ArrayCollection(
                array_merge(
                    $this->contactDetails->toArray(),
                    $contactDetails->toArray()
                )
            );
        } elseif (!$this->contactDetails->contains($contactDetails)) {
            $this->contactDetails->add($contactDetails);
        }

        return $this;
    }

    /**
     * Remove a contact details
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $contactDetails
     * @return Licence
     */
    public function removeContactDetails($contactDetails)
    {
        if ($this->contactDetails->contains($contactDetails)) {
            $this->contactDetails->remove($contactDetails);
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
            $this->documents->remove($documents);
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
            $this->licenceVehicles->remove($licenceVehicles);
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
            $this->workshops->remove($workshops);
        }

        return $this;
    }

}

<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Application Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application",
 *    indexes={
 *        @ORM\Index(name="fk_application_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_application_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_application_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_application_ref_data1_idx", columns={"licence_type"}),
 *        @ORM\Index(name="fk_application_ref_data2_idx", columns={"status"}),
 *        @ORM\Index(name="fk_application_ref_data3_idx", columns={"interim_status"}),
 *        @ORM\Index(name="fk_application_ref_data4_idx", columns={"withdrawn_reason"})
 *    }
 * )
 */
class Application implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\WithdrawnReasonManyToOne,
        Traits\LicenceTypeManyToOne,
        Traits\StatusManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Interim status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="interim_status", referencedColumnName="id", nullable=true)
     */
    protected $interimStatus;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY", inversedBy="applications")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Is variation
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_variation", nullable=false)
     */
    protected $isVariation;

    /**
     * Has entered reg
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="has_entered_reg", nullable=true)
     */
    protected $hasEnteredReg;

    /**
     * Tot auth trailers
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_trailers", nullable=true)
     */
    protected $totAuthTrailers;

    /**
     * Tot auth vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_vehicles", nullable=true)
     */
    protected $totAuthVehicles;

    /**
     * Tot auth small vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_small_vehicles", nullable=true)
     */
    protected $totAuthSmallVehicles;

    /**
     * Tot auth medium vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_medium_vehicles", nullable=true)
     */
    protected $totAuthMediumVehicles;

    /**
     * Tot auth large vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_large_vehicles", nullable=true)
     */
    protected $totAuthLargeVehicles;

    /**
     * Tot community licences
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_community_licences", nullable=true)
     */
    protected $totCommunityLicences;

    /**
     * Bankrupt
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="bankrupt", nullable=true)
     */
    protected $bankrupt;

    /**
     * Administration
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="administration", nullable=true)
     */
    protected $administration;

    /**
     * Disqualified
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="disqualified", nullable=true)
     */
    protected $disqualified;

    /**
     * Liquidation
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="liquidation", nullable=true)
     */
    protected $liquidation;

    /**
     * Receivership
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="receivership", nullable=true)
     */
    protected $receivership;

    /**
     * Insolvency confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="insolvency_confirmation", nullable=false)
     */
    protected $insolvencyConfirmation = 0;

    /**
     * Insolvency details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="insolvency_details", length=4000, nullable=true)
     */
    protected $insolvencyDetails;

    /**
     * Safety confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="safety_confirmation", nullable=false)
     */
    protected $safetyConfirmation = 1;

    /**
     * Received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="received_date", nullable=true)
     */
    protected $receivedDate;

    /**
     * Target completion date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="target_completion_date", nullable=true)
     */
    protected $targetCompletionDate;

    /**
     * Granted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="granted_date", nullable=true)
     */
    protected $grantedDate;

    /**
     * Refused date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="refused_date", nullable=true)
     */
    protected $refusedDate;

    /**
     * Withdrawn date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="withdrawn_date", nullable=true)
     */
    protected $withdrawnDate;

    /**
     * Prev has licence
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_has_licence", nullable=true)
     */
    protected $prevHasLicence;

    /**
     * Prev had licence
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_had_licence", nullable=true)
     */
    protected $prevHadLicence;

    /**
     * Prev been refused
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_been_refused", nullable=true)
     */
    protected $prevBeenRefused;

    /**
     * Prev been revoked
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_been_revoked", nullable=true)
     */
    protected $prevBeenRevoked;

    /**
     * Prev been at pi
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_been_at_pi", nullable=true)
     */
    protected $prevBeenAtPi;

    /**
     * Prev been disqualified tc
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_been_disqualified_tc", nullable=true)
     */
    protected $prevBeenDisqualifiedTc;

    /**
     * Prev purchased assets
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_purchased_assets", nullable=true)
     */
    protected $prevPurchasedAssets;

    /**
     * Override ooo
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="override_ooo", nullable=false)
     */
    protected $overrideOoo = 0;

    /**
     * Prev conviction
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_conviction", nullable=true)
     */
    protected $prevConviction;

    /**
     * Convictions confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="convictions_confirmation", nullable=false)
     */
    protected $convictionsConfirmation = 0;

    /**
     * Psv operate small vhl
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="psv_operate_small_vhl", nullable=true)
     */
    protected $psvOperateSmallVhl;

    /**
     * Psv small vhl notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="psv_small_vhl_notes", length=4000, nullable=true)
     */
    protected $psvSmallVhlNotes;

    /**
     * Psv small vhl confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="psv_small_vhl_confirmation", nullable=false)
     */
    protected $psvSmallVhlConfirmation = 0;

    /**
     * Psv no small vhl confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="psv_no_small_vhl_confirmation", nullable=false)
     */
    protected $psvNoSmallVhlConfirmation = 0;

    /**
     * Psv limousines
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="psv_limousines", nullable=false)
     */
    protected $psvLimousines = 0;

    /**
     * Psv no limousine confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="psv_no_limousine_confirmation", nullable=false)
     */
    protected $psvNoLimousineConfirmation = 0;

    /**
     * Psv only limousines confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="psv_only_limousines_confirmation", nullable=false)
     */
    protected $psvOnlyLimousinesConfirmation = 0;

    /**
     * Interim start
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="interim_start", nullable=true)
     */
    protected $interimStart;

    /**
     * Interim end
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="interim_end", nullable=true)
     */
    protected $interimEnd;

    /**
     * Interim auth vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="interim_auth_vehicles", nullable=true)
     */
    protected $interimAuthVehicles;

    /**
     * Interim auth trailers
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="interim_auth_trailers", nullable=true)
     */
    protected $interimAuthTrailers;

    /**
     * Is maintenance suitable
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_maintenance_suitable", nullable=true)
     */
    protected $isMaintenanceSuitable;

    /**
     * Application completion
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ApplicationCompletion", mappedBy="application")
     */
    protected $applicationCompletions;

    /**
     * Operating centre
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ApplicationOperatingCentre", mappedBy="application")
     */
    protected $operatingCentres;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="application")
     */
    protected $documents;

    /**
     * Opposition
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Opposition", mappedBy="application")
     */
    protected $oppositions;

    /**
     * Previous conviction
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PreviousConviction", mappedBy="application")
     */
    protected $previousConvictions;

    /**
     * Previous licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PreviousLicence", mappedBy="application")
     */
    protected $previousLicences;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->applicationCompletions = new ArrayCollection();
        $this->operatingCentres = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->oppositions = new ArrayCollection();
        $this->previousConvictions = new ArrayCollection();
        $this->previousLicences = new ArrayCollection();
    }

    /**
     * Set the interim status
     *
     * @param \Olcs\Db\Entity\RefData $interimStatus
     * @return Application
     */
    public function setInterimStatus($interimStatus)
    {
        $this->interimStatus = $interimStatus;

        return $this;
    }

    /**
     * Get the interim status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getInterimStatus()
    {
        return $this->interimStatus;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return Application
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
     * Set the is variation
     *
     * @param boolean $isVariation
     * @return Application
     */
    public function setIsVariation($isVariation)
    {
        $this->isVariation = $isVariation;

        return $this;
    }

    /**
     * Get the is variation
     *
     * @return boolean
     */
    public function getIsVariation()
    {
        return $this->isVariation;
    }

    /**
     * Set the has entered reg
     *
     * @param string $hasEnteredReg
     * @return Application
     */
    public function setHasEnteredReg($hasEnteredReg)
    {
        $this->hasEnteredReg = $hasEnteredReg;

        return $this;
    }

    /**
     * Get the has entered reg
     *
     * @return string
     */
    public function getHasEnteredReg()
    {
        return $this->hasEnteredReg;
    }

    /**
     * Set the tot auth trailers
     *
     * @param int $totAuthTrailers
     * @return Application
     */
    public function setTotAuthTrailers($totAuthTrailers)
    {
        $this->totAuthTrailers = $totAuthTrailers;

        return $this;
    }

    /**
     * Get the tot auth trailers
     *
     * @return int
     */
    public function getTotAuthTrailers()
    {
        return $this->totAuthTrailers;
    }

    /**
     * Set the tot auth vehicles
     *
     * @param int $totAuthVehicles
     * @return Application
     */
    public function setTotAuthVehicles($totAuthVehicles)
    {
        $this->totAuthVehicles = $totAuthVehicles;

        return $this;
    }

    /**
     * Get the tot auth vehicles
     *
     * @return int
     */
    public function getTotAuthVehicles()
    {
        return $this->totAuthVehicles;
    }

    /**
     * Set the tot auth small vehicles
     *
     * @param int $totAuthSmallVehicles
     * @return Application
     */
    public function setTotAuthSmallVehicles($totAuthSmallVehicles)
    {
        $this->totAuthSmallVehicles = $totAuthSmallVehicles;

        return $this;
    }

    /**
     * Get the tot auth small vehicles
     *
     * @return int
     */
    public function getTotAuthSmallVehicles()
    {
        return $this->totAuthSmallVehicles;
    }

    /**
     * Set the tot auth medium vehicles
     *
     * @param int $totAuthMediumVehicles
     * @return Application
     */
    public function setTotAuthMediumVehicles($totAuthMediumVehicles)
    {
        $this->totAuthMediumVehicles = $totAuthMediumVehicles;

        return $this;
    }

    /**
     * Get the tot auth medium vehicles
     *
     * @return int
     */
    public function getTotAuthMediumVehicles()
    {
        return $this->totAuthMediumVehicles;
    }

    /**
     * Set the tot auth large vehicles
     *
     * @param int $totAuthLargeVehicles
     * @return Application
     */
    public function setTotAuthLargeVehicles($totAuthLargeVehicles)
    {
        $this->totAuthLargeVehicles = $totAuthLargeVehicles;

        return $this;
    }

    /**
     * Get the tot auth large vehicles
     *
     * @return int
     */
    public function getTotAuthLargeVehicles()
    {
        return $this->totAuthLargeVehicles;
    }

    /**
     * Set the tot community licences
     *
     * @param int $totCommunityLicences
     * @return Application
     */
    public function setTotCommunityLicences($totCommunityLicences)
    {
        $this->totCommunityLicences = $totCommunityLicences;

        return $this;
    }

    /**
     * Get the tot community licences
     *
     * @return int
     */
    public function getTotCommunityLicences()
    {
        return $this->totCommunityLicences;
    }

    /**
     * Set the bankrupt
     *
     * @param string $bankrupt
     * @return Application
     */
    public function setBankrupt($bankrupt)
    {
        $this->bankrupt = $bankrupt;

        return $this;
    }

    /**
     * Get the bankrupt
     *
     * @return string
     */
    public function getBankrupt()
    {
        return $this->bankrupt;
    }

    /**
     * Set the administration
     *
     * @param string $administration
     * @return Application
     */
    public function setAdministration($administration)
    {
        $this->administration = $administration;

        return $this;
    }

    /**
     * Get the administration
     *
     * @return string
     */
    public function getAdministration()
    {
        return $this->administration;
    }

    /**
     * Set the disqualified
     *
     * @param string $disqualified
     * @return Application
     */
    public function setDisqualified($disqualified)
    {
        $this->disqualified = $disqualified;

        return $this;
    }

    /**
     * Get the disqualified
     *
     * @return string
     */
    public function getDisqualified()
    {
        return $this->disqualified;
    }

    /**
     * Set the liquidation
     *
     * @param string $liquidation
     * @return Application
     */
    public function setLiquidation($liquidation)
    {
        $this->liquidation = $liquidation;

        return $this;
    }

    /**
     * Get the liquidation
     *
     * @return string
     */
    public function getLiquidation()
    {
        return $this->liquidation;
    }

    /**
     * Set the receivership
     *
     * @param string $receivership
     * @return Application
     */
    public function setReceivership($receivership)
    {
        $this->receivership = $receivership;

        return $this;
    }

    /**
     * Get the receivership
     *
     * @return string
     */
    public function getReceivership()
    {
        return $this->receivership;
    }

    /**
     * Set the insolvency confirmation
     *
     * @param string $insolvencyConfirmation
     * @return Application
     */
    public function setInsolvencyConfirmation($insolvencyConfirmation)
    {
        $this->insolvencyConfirmation = $insolvencyConfirmation;

        return $this;
    }

    /**
     * Get the insolvency confirmation
     *
     * @return string
     */
    public function getInsolvencyConfirmation()
    {
        return $this->insolvencyConfirmation;
    }

    /**
     * Set the insolvency details
     *
     * @param string $insolvencyDetails
     * @return Application
     */
    public function setInsolvencyDetails($insolvencyDetails)
    {
        $this->insolvencyDetails = $insolvencyDetails;

        return $this;
    }

    /**
     * Get the insolvency details
     *
     * @return string
     */
    public function getInsolvencyDetails()
    {
        return $this->insolvencyDetails;
    }

    /**
     * Set the safety confirmation
     *
     * @param string $safetyConfirmation
     * @return Application
     */
    public function setSafetyConfirmation($safetyConfirmation)
    {
        $this->safetyConfirmation = $safetyConfirmation;

        return $this;
    }

    /**
     * Get the safety confirmation
     *
     * @return string
     */
    public function getSafetyConfirmation()
    {
        return $this->safetyConfirmation;
    }

    /**
     * Set the received date
     *
     * @param \DateTime $receivedDate
     * @return Application
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
     * Set the target completion date
     *
     * @param \DateTime $targetCompletionDate
     * @return Application
     */
    public function setTargetCompletionDate($targetCompletionDate)
    {
        $this->targetCompletionDate = $targetCompletionDate;

        return $this;
    }

    /**
     * Get the target completion date
     *
     * @return \DateTime
     */
    public function getTargetCompletionDate()
    {
        return $this->targetCompletionDate;
    }

    /**
     * Set the granted date
     *
     * @param \DateTime $grantedDate
     * @return Application
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
     * Set the refused date
     *
     * @param \DateTime $refusedDate
     * @return Application
     */
    public function setRefusedDate($refusedDate)
    {
        $this->refusedDate = $refusedDate;

        return $this;
    }

    /**
     * Get the refused date
     *
     * @return \DateTime
     */
    public function getRefusedDate()
    {
        return $this->refusedDate;
    }

    /**
     * Set the withdrawn date
     *
     * @param \DateTime $withdrawnDate
     * @return Application
     */
    public function setWithdrawnDate($withdrawnDate)
    {
        $this->withdrawnDate = $withdrawnDate;

        return $this;
    }

    /**
     * Get the withdrawn date
     *
     * @return \DateTime
     */
    public function getWithdrawnDate()
    {
        return $this->withdrawnDate;
    }

    /**
     * Set the prev has licence
     *
     * @param string $prevHasLicence
     * @return Application
     */
    public function setPrevHasLicence($prevHasLicence)
    {
        $this->prevHasLicence = $prevHasLicence;

        return $this;
    }

    /**
     * Get the prev has licence
     *
     * @return string
     */
    public function getPrevHasLicence()
    {
        return $this->prevHasLicence;
    }

    /**
     * Set the prev had licence
     *
     * @param string $prevHadLicence
     * @return Application
     */
    public function setPrevHadLicence($prevHadLicence)
    {
        $this->prevHadLicence = $prevHadLicence;

        return $this;
    }

    /**
     * Get the prev had licence
     *
     * @return string
     */
    public function getPrevHadLicence()
    {
        return $this->prevHadLicence;
    }

    /**
     * Set the prev been refused
     *
     * @param string $prevBeenRefused
     * @return Application
     */
    public function setPrevBeenRefused($prevBeenRefused)
    {
        $this->prevBeenRefused = $prevBeenRefused;

        return $this;
    }

    /**
     * Get the prev been refused
     *
     * @return string
     */
    public function getPrevBeenRefused()
    {
        return $this->prevBeenRefused;
    }

    /**
     * Set the prev been revoked
     *
     * @param string $prevBeenRevoked
     * @return Application
     */
    public function setPrevBeenRevoked($prevBeenRevoked)
    {
        $this->prevBeenRevoked = $prevBeenRevoked;

        return $this;
    }

    /**
     * Get the prev been revoked
     *
     * @return string
     */
    public function getPrevBeenRevoked()
    {
        return $this->prevBeenRevoked;
    }

    /**
     * Set the prev been at pi
     *
     * @param string $prevBeenAtPi
     * @return Application
     */
    public function setPrevBeenAtPi($prevBeenAtPi)
    {
        $this->prevBeenAtPi = $prevBeenAtPi;

        return $this;
    }

    /**
     * Get the prev been at pi
     *
     * @return string
     */
    public function getPrevBeenAtPi()
    {
        return $this->prevBeenAtPi;
    }

    /**
     * Set the prev been disqualified tc
     *
     * @param string $prevBeenDisqualifiedTc
     * @return Application
     */
    public function setPrevBeenDisqualifiedTc($prevBeenDisqualifiedTc)
    {
        $this->prevBeenDisqualifiedTc = $prevBeenDisqualifiedTc;

        return $this;
    }

    /**
     * Get the prev been disqualified tc
     *
     * @return string
     */
    public function getPrevBeenDisqualifiedTc()
    {
        return $this->prevBeenDisqualifiedTc;
    }

    /**
     * Set the prev purchased assets
     *
     * @param string $prevPurchasedAssets
     * @return Application
     */
    public function setPrevPurchasedAssets($prevPurchasedAssets)
    {
        $this->prevPurchasedAssets = $prevPurchasedAssets;

        return $this;
    }

    /**
     * Get the prev purchased assets
     *
     * @return string
     */
    public function getPrevPurchasedAssets()
    {
        return $this->prevPurchasedAssets;
    }

    /**
     * Set the override ooo
     *
     * @param string $overrideOoo
     * @return Application
     */
    public function setOverrideOoo($overrideOoo)
    {
        $this->overrideOoo = $overrideOoo;

        return $this;
    }

    /**
     * Get the override ooo
     *
     * @return string
     */
    public function getOverrideOoo()
    {
        return $this->overrideOoo;
    }

    /**
     * Set the prev conviction
     *
     * @param string $prevConviction
     * @return Application
     */
    public function setPrevConviction($prevConviction)
    {
        $this->prevConviction = $prevConviction;

        return $this;
    }

    /**
     * Get the prev conviction
     *
     * @return string
     */
    public function getPrevConviction()
    {
        return $this->prevConviction;
    }

    /**
     * Set the convictions confirmation
     *
     * @param string $convictionsConfirmation
     * @return Application
     */
    public function setConvictionsConfirmation($convictionsConfirmation)
    {
        $this->convictionsConfirmation = $convictionsConfirmation;

        return $this;
    }

    /**
     * Get the convictions confirmation
     *
     * @return string
     */
    public function getConvictionsConfirmation()
    {
        return $this->convictionsConfirmation;
    }

    /**
     * Set the psv operate small vhl
     *
     * @param string $psvOperateSmallVhl
     * @return Application
     */
    public function setPsvOperateSmallVhl($psvOperateSmallVhl)
    {
        $this->psvOperateSmallVhl = $psvOperateSmallVhl;

        return $this;
    }

    /**
     * Get the psv operate small vhl
     *
     * @return string
     */
    public function getPsvOperateSmallVhl()
    {
        return $this->psvOperateSmallVhl;
    }

    /**
     * Set the psv small vhl notes
     *
     * @param string $psvSmallVhlNotes
     * @return Application
     */
    public function setPsvSmallVhlNotes($psvSmallVhlNotes)
    {
        $this->psvSmallVhlNotes = $psvSmallVhlNotes;

        return $this;
    }

    /**
     * Get the psv small vhl notes
     *
     * @return string
     */
    public function getPsvSmallVhlNotes()
    {
        return $this->psvSmallVhlNotes;
    }

    /**
     * Set the psv small vhl confirmation
     *
     * @param string $psvSmallVhlConfirmation
     * @return Application
     */
    public function setPsvSmallVhlConfirmation($psvSmallVhlConfirmation)
    {
        $this->psvSmallVhlConfirmation = $psvSmallVhlConfirmation;

        return $this;
    }

    /**
     * Get the psv small vhl confirmation
     *
     * @return string
     */
    public function getPsvSmallVhlConfirmation()
    {
        return $this->psvSmallVhlConfirmation;
    }

    /**
     * Set the psv no small vhl confirmation
     *
     * @param string $psvNoSmallVhlConfirmation
     * @return Application
     */
    public function setPsvNoSmallVhlConfirmation($psvNoSmallVhlConfirmation)
    {
        $this->psvNoSmallVhlConfirmation = $psvNoSmallVhlConfirmation;

        return $this;
    }

    /**
     * Get the psv no small vhl confirmation
     *
     * @return string
     */
    public function getPsvNoSmallVhlConfirmation()
    {
        return $this->psvNoSmallVhlConfirmation;
    }

    /**
     * Set the psv limousines
     *
     * @param string $psvLimousines
     * @return Application
     */
    public function setPsvLimousines($psvLimousines)
    {
        $this->psvLimousines = $psvLimousines;

        return $this;
    }

    /**
     * Get the psv limousines
     *
     * @return string
     */
    public function getPsvLimousines()
    {
        return $this->psvLimousines;
    }

    /**
     * Set the psv no limousine confirmation
     *
     * @param string $psvNoLimousineConfirmation
     * @return Application
     */
    public function setPsvNoLimousineConfirmation($psvNoLimousineConfirmation)
    {
        $this->psvNoLimousineConfirmation = $psvNoLimousineConfirmation;

        return $this;
    }

    /**
     * Get the psv no limousine confirmation
     *
     * @return string
     */
    public function getPsvNoLimousineConfirmation()
    {
        return $this->psvNoLimousineConfirmation;
    }

    /**
     * Set the psv only limousines confirmation
     *
     * @param string $psvOnlyLimousinesConfirmation
     * @return Application
     */
    public function setPsvOnlyLimousinesConfirmation($psvOnlyLimousinesConfirmation)
    {
        $this->psvOnlyLimousinesConfirmation = $psvOnlyLimousinesConfirmation;

        return $this;
    }

    /**
     * Get the psv only limousines confirmation
     *
     * @return string
     */
    public function getPsvOnlyLimousinesConfirmation()
    {
        return $this->psvOnlyLimousinesConfirmation;
    }

    /**
     * Set the interim start
     *
     * @param \DateTime $interimStart
     * @return Application
     */
    public function setInterimStart($interimStart)
    {
        $this->interimStart = $interimStart;

        return $this;
    }

    /**
     * Get the interim start
     *
     * @return \DateTime
     */
    public function getInterimStart()
    {
        return $this->interimStart;
    }

    /**
     * Set the interim end
     *
     * @param \DateTime $interimEnd
     * @return Application
     */
    public function setInterimEnd($interimEnd)
    {
        $this->interimEnd = $interimEnd;

        return $this;
    }

    /**
     * Get the interim end
     *
     * @return \DateTime
     */
    public function getInterimEnd()
    {
        return $this->interimEnd;
    }

    /**
     * Set the interim auth vehicles
     *
     * @param int $interimAuthVehicles
     * @return Application
     */
    public function setInterimAuthVehicles($interimAuthVehicles)
    {
        $this->interimAuthVehicles = $interimAuthVehicles;

        return $this;
    }

    /**
     * Get the interim auth vehicles
     *
     * @return int
     */
    public function getInterimAuthVehicles()
    {
        return $this->interimAuthVehicles;
    }

    /**
     * Set the interim auth trailers
     *
     * @param int $interimAuthTrailers
     * @return Application
     */
    public function setInterimAuthTrailers($interimAuthTrailers)
    {
        $this->interimAuthTrailers = $interimAuthTrailers;

        return $this;
    }

    /**
     * Get the interim auth trailers
     *
     * @return int
     */
    public function getInterimAuthTrailers()
    {
        return $this->interimAuthTrailers;
    }

    /**
     * Set the is maintenance suitable
     *
     * @param string $isMaintenanceSuitable
     * @return Application
     */
    public function setIsMaintenanceSuitable($isMaintenanceSuitable)
    {
        $this->isMaintenanceSuitable = $isMaintenanceSuitable;

        return $this;
    }

    /**
     * Get the is maintenance suitable
     *
     * @return string
     */
    public function getIsMaintenanceSuitable()
    {
        return $this->isMaintenanceSuitable;
    }

    /**
     * Set the application completion
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationCompletions
     * @return Application
     */
    public function setApplicationCompletions($applicationCompletions)
    {
        $this->applicationCompletions = $applicationCompletions;

        return $this;
    }

    /**
     * Get the application completions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getApplicationCompletions()
    {
        return $this->applicationCompletions;
    }

    /**
     * Add a application completions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationCompletions
     * @return Application
     */
    public function addApplicationCompletions($applicationCompletions)
    {
        if ($applicationCompletions instanceof ArrayCollection) {
            $this->applicationCompletions = new ArrayCollection(
                array_merge(
                    $this->applicationCompletions->toArray(),
                    $applicationCompletions->toArray()
                )
            );
        } elseif (!$this->applicationCompletions->contains($applicationCompletions)) {
            $this->applicationCompletions->add($applicationCompletions);
        }

        return $this;
    }

    /**
     * Remove a application completions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationCompletions
     * @return Application
     */
    public function removeApplicationCompletions($applicationCompletions)
    {
        if ($this->applicationCompletions->contains($applicationCompletions)) {
            $this->applicationCompletions->removeElement($applicationCompletions);
        }

        return $this;
    }

    /**
     * Set the operating centre
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return Application
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
     * @return Application
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
     * @return Application
     */
    public function removeOperatingCentres($operatingCentres)
    {
        if ($this->operatingCentres->contains($operatingCentres)) {
            $this->operatingCentres->removeElement($operatingCentres);
        }

        return $this;
    }

    /**
     * Set the document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Application
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
     * @return Application
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
     * @return Application
     */
    public function removeDocuments($documents)
    {
        if ($this->documents->contains($documents)) {
            $this->documents->removeElement($documents);
        }

        return $this;
    }

    /**
     * Set the opposition
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
     * @return Application
     */
    public function setOppositions($oppositions)
    {
        $this->oppositions = $oppositions;

        return $this;
    }

    /**
     * Get the oppositions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOppositions()
    {
        return $this->oppositions;
    }

    /**
     * Add a oppositions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
     * @return Application
     */
    public function addOppositions($oppositions)
    {
        if ($oppositions instanceof ArrayCollection) {
            $this->oppositions = new ArrayCollection(
                array_merge(
                    $this->oppositions->toArray(),
                    $oppositions->toArray()
                )
            );
        } elseif (!$this->oppositions->contains($oppositions)) {
            $this->oppositions->add($oppositions);
        }

        return $this;
    }

    /**
     * Remove a oppositions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
     * @return Application
     */
    public function removeOppositions($oppositions)
    {
        if ($this->oppositions->contains($oppositions)) {
            $this->oppositions->removeElement($oppositions);
        }

        return $this;
    }

    /**
     * Set the previous conviction
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousConvictions
     * @return Application
     */
    public function setPreviousConvictions($previousConvictions)
    {
        $this->previousConvictions = $previousConvictions;

        return $this;
    }

    /**
     * Get the previous convictions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPreviousConvictions()
    {
        return $this->previousConvictions;
    }

    /**
     * Add a previous convictions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousConvictions
     * @return Application
     */
    public function addPreviousConvictions($previousConvictions)
    {
        if ($previousConvictions instanceof ArrayCollection) {
            $this->previousConvictions = new ArrayCollection(
                array_merge(
                    $this->previousConvictions->toArray(),
                    $previousConvictions->toArray()
                )
            );
        } elseif (!$this->previousConvictions->contains($previousConvictions)) {
            $this->previousConvictions->add($previousConvictions);
        }

        return $this;
    }

    /**
     * Remove a previous convictions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousConvictions
     * @return Application
     */
    public function removePreviousConvictions($previousConvictions)
    {
        if ($this->previousConvictions->contains($previousConvictions)) {
            $this->previousConvictions->removeElement($previousConvictions);
        }

        return $this;
    }

    /**
     * Set the previous licence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousLicences
     * @return Application
     */
    public function setPreviousLicences($previousLicences)
    {
        $this->previousLicences = $previousLicences;

        return $this;
    }

    /**
     * Get the previous licences
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPreviousLicences()
    {
        return $this->previousLicences;
    }

    /**
     * Add a previous licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousLicences
     * @return Application
     */
    public function addPreviousLicences($previousLicences)
    {
        if ($previousLicences instanceof ArrayCollection) {
            $this->previousLicences = new ArrayCollection(
                array_merge(
                    $this->previousLicences->toArray(),
                    $previousLicences->toArray()
                )
            );
        } elseif (!$this->previousLicences->contains($previousLicences)) {
            $this->previousLicences->add($previousLicences);
        }

        return $this;
    }

    /**
     * Remove a previous licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousLicences
     * @return Application
     */
    public function removePreviousLicences($previousLicences)
    {
        if ($this->previousLicences->contains($previousLicences)) {
            $this->previousLicences->removeElement($previousLicences);
        }

        return $this;
    }
}

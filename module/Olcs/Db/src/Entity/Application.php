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
        Traits\WithdrawnReasonManyToOne,
        Traits\StatusManyToOne,
        Traits\LicenceTypeManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\TotAuthTrailersField,
        Traits\TotAuthVehiclesField,
        Traits\TotAuthSmallVehiclesField,
        Traits\TotAuthMediumVehiclesField,
        Traits\TotAuthLargeVehiclesField,
        Traits\TotCommunityLicencesField,
        Traits\ReceivedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Interim status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="interim_status", referencedColumnName="id")
     */
    protected $interimStatus;

    /**
     * Application action ref
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\ApplicationActionRef", inversedBy="applications")
     * @ORM\JoinTable(name="application_action",
     *     joinColumns={
     *         @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="application_action_ref_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $applicationActionRefs;

    /**
     * Bankrupt
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="bankrupt", nullable=false)
     */
    protected $bankrupt = 0;

    /**
     * Administration
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="administration", nullable=false)
     */
    protected $administration = 0;

    /**
     * Disqualified
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="disqualified", nullable=false)
     */
    protected $disqualified = 0;

    /**
     * Liquidation
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="liquidation", nullable=false)
     */
    protected $liquidation = 0;

    /**
     * Receivership
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="receivership", nullable=false)
     */
    protected $receivership = 0;

    /**
     * Insolvency confirmation
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="insolvency_confirmation", nullable=false)
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
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="safety_confirmation", nullable=false)
     */
    protected $safetyConfirmation = 1;

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
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="prev_has_licence", nullable=true)
     */
    protected $prevHasLicence;

    /**
     * Prev had licence
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="prev_had_licence", nullable=true)
     */
    protected $prevHadLicence;

    /**
     * Prev been disqualified eu
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="prev_been_disqualified_eu", nullable=true)
     */
    protected $prevBeenDisqualifiedEu;

    /**
     * Prev been revoked
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="prev_been_revoked", nullable=true)
     */
    protected $prevBeenRevoked;

    /**
     * Prev been at pi
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="prev_been_at_pi", nullable=true)
     */
    protected $prevBeenAtPi;

    /**
     * Prev been disqualified tc
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="prev_been_disqualified_tc", nullable=true)
     */
    protected $prevBeenDisqualifiedTc;

    /**
     * Prev purchased assets
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="prev_purchased_assets", nullable=true)
     */
    protected $prevPurchasedAssets;

    /**
     * Override ooo
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="override_ooo", nullable=false)
     */
    protected $overrideOoo = 0;

    /**
     * Prev conviction
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="prev_conviction", nullable=false)
     */
    protected $prevConviction = 0;

    /**
     * Convictions confirmation
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="convictions_confirmation", nullable=false)
     */
    protected $convictionsConfirmation = 0;

    /**
     * Psv operate small vhl
     *
     * @var boolean
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
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="psv_small_vhl_confirmation", nullable=false)
     */
    protected $psvSmallVhlConfirmation = 0;

    /**
     * Psv no small vhl confirmation
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="psv_no_small_vhl_confirmation", nullable=false)
     */
    protected $psvNoSmallVhlConfirmation = 0;

    /**
     * Psv limosines
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="psv_limosines", nullable=false)
     */
    protected $psvLimosines = 0;

    /**
     * Psv no limosine confirmation
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="psv_no_limosine_confirmation", nullable=false)
     */
    protected $psvNoLimosineConfirmation = 0;

    /**
     * Psv only limosines confirmation
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="psv_only_limosines_confirmation", nullable=false)
     */
    protected $psvOnlyLimosinesConfirmation = 0;

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
     * Initialise the collections
     */
    public function __construct()
    {
        $this->applicationActionRefs = new ArrayCollection();
    }

    /**
     * Set the interim status
     *
     * @param \Olcs\Db\Entity\RefData $interimStatus
     * @return \Olcs\Db\Entity\Application
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
     * Set the application action ref
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationActionRefs

     * @return \Olcs\Db\Entity\Application
     */
    public function setApplicationActionRefs($applicationActionRefs)
    {
        $this->applicationActionRefs = $applicationActionRefs;

        return $this;
    }

    /**
     * Get the application action ref
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getApplicationActionRefs()
    {
        return $this->applicationActionRefs;
    }

    /**
     * Set the bankrupt
     *
     * @param boolean $bankrupt
     * @return \Olcs\Db\Entity\Application
     */
    public function setBankrupt($bankrupt)
    {
        $this->bankrupt = $bankrupt;

        return $this;
    }

    /**
     * Get the bankrupt
     *
     * @return boolean
     */
    public function getBankrupt()
    {
        return $this->bankrupt;
    }

    /**
     * Set the administration
     *
     * @param boolean $administration
     * @return \Olcs\Db\Entity\Application
     */
    public function setAdministration($administration)
    {
        $this->administration = $administration;

        return $this;
    }

    /**
     * Get the administration
     *
     * @return boolean
     */
    public function getAdministration()
    {
        return $this->administration;
    }

    /**
     * Set the disqualified
     *
     * @param boolean $disqualified
     * @return \Olcs\Db\Entity\Application
     */
    public function setDisqualified($disqualified)
    {
        $this->disqualified = $disqualified;

        return $this;
    }

    /**
     * Get the disqualified
     *
     * @return boolean
     */
    public function getDisqualified()
    {
        return $this->disqualified;
    }

    /**
     * Set the liquidation
     *
     * @param boolean $liquidation
     * @return \Olcs\Db\Entity\Application
     */
    public function setLiquidation($liquidation)
    {
        $this->liquidation = $liquidation;

        return $this;
    }

    /**
     * Get the liquidation
     *
     * @return boolean
     */
    public function getLiquidation()
    {
        return $this->liquidation;
    }

    /**
     * Set the receivership
     *
     * @param boolean $receivership
     * @return \Olcs\Db\Entity\Application
     */
    public function setReceivership($receivership)
    {
        $this->receivership = $receivership;

        return $this;
    }

    /**
     * Get the receivership
     *
     * @return boolean
     */
    public function getReceivership()
    {
        return $this->receivership;
    }

    /**
     * Set the insolvency confirmation
     *
     * @param boolean $insolvencyConfirmation
     * @return \Olcs\Db\Entity\Application
     */
    public function setInsolvencyConfirmation($insolvencyConfirmation)
    {
        $this->insolvencyConfirmation = $insolvencyConfirmation;

        return $this;
    }

    /**
     * Get the insolvency confirmation
     *
     * @return boolean
     */
    public function getInsolvencyConfirmation()
    {
        return $this->insolvencyConfirmation;
    }

    /**
     * Set the insolvency details
     *
     * @param string $insolvencyDetails
     * @return \Olcs\Db\Entity\Application
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
     * @param boolean $safetyConfirmation
     * @return \Olcs\Db\Entity\Application
     */
    public function setSafetyConfirmation($safetyConfirmation)
    {
        $this->safetyConfirmation = $safetyConfirmation;

        return $this;
    }

    /**
     * Get the safety confirmation
     *
     * @return boolean
     */
    public function getSafetyConfirmation()
    {
        return $this->safetyConfirmation;
    }

    /**
     * Set the target completion date
     *
     * @param \DateTime $targetCompletionDate
     * @return \Olcs\Db\Entity\Application
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
     * @return \Olcs\Db\Entity\Application
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
     * @return \Olcs\Db\Entity\Application
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
     * @return \Olcs\Db\Entity\Application
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
     * @param boolean $prevHasLicence
     * @return \Olcs\Db\Entity\Application
     */
    public function setPrevHasLicence($prevHasLicence)
    {
        $this->prevHasLicence = $prevHasLicence;

        return $this;
    }

    /**
     * Get the prev has licence
     *
     * @return boolean
     */
    public function getPrevHasLicence()
    {
        return $this->prevHasLicence;
    }

    /**
     * Set the prev had licence
     *
     * @param boolean $prevHadLicence
     * @return \Olcs\Db\Entity\Application
     */
    public function setPrevHadLicence($prevHadLicence)
    {
        $this->prevHadLicence = $prevHadLicence;

        return $this;
    }

    /**
     * Get the prev had licence
     *
     * @return boolean
     */
    public function getPrevHadLicence()
    {
        return $this->prevHadLicence;
    }

    /**
     * Set the prev been disqualified eu
     *
     * @param boolean $prevBeenDisqualifiedEu
     * @return \Olcs\Db\Entity\Application
     */
    public function setPrevBeenDisqualifiedEu($prevBeenDisqualifiedEu)
    {
        $this->prevBeenDisqualifiedEu = $prevBeenDisqualifiedEu;

        return $this;
    }

    /**
     * Get the prev been disqualified eu
     *
     * @return boolean
     */
    public function getPrevBeenDisqualifiedEu()
    {
        return $this->prevBeenDisqualifiedEu;
    }

    /**
     * Set the prev been revoked
     *
     * @param boolean $prevBeenRevoked
     * @return \Olcs\Db\Entity\Application
     */
    public function setPrevBeenRevoked($prevBeenRevoked)
    {
        $this->prevBeenRevoked = $prevBeenRevoked;

        return $this;
    }

    /**
     * Get the prev been revoked
     *
     * @return boolean
     */
    public function getPrevBeenRevoked()
    {
        return $this->prevBeenRevoked;
    }

    /**
     * Set the prev been at pi
     *
     * @param boolean $prevBeenAtPi
     * @return \Olcs\Db\Entity\Application
     */
    public function setPrevBeenAtPi($prevBeenAtPi)
    {
        $this->prevBeenAtPi = $prevBeenAtPi;

        return $this;
    }

    /**
     * Get the prev been at pi
     *
     * @return boolean
     */
    public function getPrevBeenAtPi()
    {
        return $this->prevBeenAtPi;
    }

    /**
     * Set the prev been disqualified tc
     *
     * @param boolean $prevBeenDisqualifiedTc
     * @return \Olcs\Db\Entity\Application
     */
    public function setPrevBeenDisqualifiedTc($prevBeenDisqualifiedTc)
    {
        $this->prevBeenDisqualifiedTc = $prevBeenDisqualifiedTc;

        return $this;
    }

    /**
     * Get the prev been disqualified tc
     *
     * @return boolean
     */
    public function getPrevBeenDisqualifiedTc()
    {
        return $this->prevBeenDisqualifiedTc;
    }

    /**
     * Set the prev purchased assets
     *
     * @param boolean $prevPurchasedAssets
     * @return \Olcs\Db\Entity\Application
     */
    public function setPrevPurchasedAssets($prevPurchasedAssets)
    {
        $this->prevPurchasedAssets = $prevPurchasedAssets;

        return $this;
    }

    /**
     * Get the prev purchased assets
     *
     * @return boolean
     */
    public function getPrevPurchasedAssets()
    {
        return $this->prevPurchasedAssets;
    }

    /**
     * Set the override ooo
     *
     * @param boolean $overrideOoo
     * @return \Olcs\Db\Entity\Application
     */
    public function setOverrideOoo($overrideOoo)
    {
        $this->overrideOoo = $overrideOoo;

        return $this;
    }

    /**
     * Get the override ooo
     *
     * @return boolean
     */
    public function getOverrideOoo()
    {
        return $this->overrideOoo;
    }

    /**
     * Set the prev conviction
     *
     * @param boolean $prevConviction
     * @return \Olcs\Db\Entity\Application
     */
    public function setPrevConviction($prevConviction)
    {
        $this->prevConviction = $prevConviction;

        return $this;
    }

    /**
     * Get the prev conviction
     *
     * @return boolean
     */
    public function getPrevConviction()
    {
        return $this->prevConviction;
    }

    /**
     * Set the convictions confirmation
     *
     * @param boolean $convictionsConfirmation
     * @return \Olcs\Db\Entity\Application
     */
    public function setConvictionsConfirmation($convictionsConfirmation)
    {
        $this->convictionsConfirmation = $convictionsConfirmation;

        return $this;
    }

    /**
     * Get the convictions confirmation
     *
     * @return boolean
     */
    public function getConvictionsConfirmation()
    {
        return $this->convictionsConfirmation;
    }

    /**
     * Set the psv operate small vhl
     *
     * @param boolean $psvOperateSmallVhl
     * @return \Olcs\Db\Entity\Application
     */
    public function setPsvOperateSmallVhl($psvOperateSmallVhl)
    {
        $this->psvOperateSmallVhl = $psvOperateSmallVhl;

        return $this;
    }

    /**
     * Get the psv operate small vhl
     *
     * @return boolean
     */
    public function getPsvOperateSmallVhl()
    {
        return $this->psvOperateSmallVhl;
    }

    /**
     * Set the psv small vhl notes
     *
     * @param string $psvSmallVhlNotes
     * @return \Olcs\Db\Entity\Application
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
     * @param boolean $psvSmallVhlConfirmation
     * @return \Olcs\Db\Entity\Application
     */
    public function setPsvSmallVhlConfirmation($psvSmallVhlConfirmation)
    {
        $this->psvSmallVhlConfirmation = $psvSmallVhlConfirmation;

        return $this;
    }

    /**
     * Get the psv small vhl confirmation
     *
     * @return boolean
     */
    public function getPsvSmallVhlConfirmation()
    {
        return $this->psvSmallVhlConfirmation;
    }

    /**
     * Set the psv no small vhl confirmation
     *
     * @param boolean $psvNoSmallVhlConfirmation
     * @return \Olcs\Db\Entity\Application
     */
    public function setPsvNoSmallVhlConfirmation($psvNoSmallVhlConfirmation)
    {
        $this->psvNoSmallVhlConfirmation = $psvNoSmallVhlConfirmation;

        return $this;
    }

    /**
     * Get the psv no small vhl confirmation
     *
     * @return boolean
     */
    public function getPsvNoSmallVhlConfirmation()
    {
        return $this->psvNoSmallVhlConfirmation;
    }

    /**
     * Set the psv limosines
     *
     * @param boolean $psvLimosines
     * @return \Olcs\Db\Entity\Application
     */
    public function setPsvLimosines($psvLimosines)
    {
        $this->psvLimosines = $psvLimosines;

        return $this;
    }

    /**
     * Get the psv limosines
     *
     * @return boolean
     */
    public function getPsvLimosines()
    {
        return $this->psvLimosines;
    }

    /**
     * Set the psv no limosine confirmation
     *
     * @param boolean $psvNoLimosineConfirmation
     * @return \Olcs\Db\Entity\Application
     */
    public function setPsvNoLimosineConfirmation($psvNoLimosineConfirmation)
    {
        $this->psvNoLimosineConfirmation = $psvNoLimosineConfirmation;

        return $this;
    }

    /**
     * Get the psv no limosine confirmation
     *
     * @return boolean
     */
    public function getPsvNoLimosineConfirmation()
    {
        return $this->psvNoLimosineConfirmation;
    }

    /**
     * Set the psv only limosines confirmation
     *
     * @param boolean $psvOnlyLimosinesConfirmation
     * @return \Olcs\Db\Entity\Application
     */
    public function setPsvOnlyLimosinesConfirmation($psvOnlyLimosinesConfirmation)
    {
        $this->psvOnlyLimosinesConfirmation = $psvOnlyLimosinesConfirmation;

        return $this;
    }

    /**
     * Get the psv only limosines confirmation
     *
     * @return boolean
     */
    public function getPsvOnlyLimosinesConfirmation()
    {
        return $this->psvOnlyLimosinesConfirmation;
    }

    /**
     * Set the interim start
     *
     * @param \DateTime $interimStart
     * @return \Olcs\Db\Entity\Application
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
     * @return \Olcs\Db\Entity\Application
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
     * @return \Olcs\Db\Entity\Application
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
     * @return \Olcs\Db\Entity\Application
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
}

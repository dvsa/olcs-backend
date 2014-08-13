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
        Traits\TotAuthTrailersField,
        Traits\TotAuthVehiclesField,
        Traits\TotAuthSmallVehiclesField,
        Traits\TotAuthMediumVehiclesField,
        Traits\TotAuthLargeVehiclesField,
        Traits\TotCommunityLicencesField,
        Traits\ReceivedDateField,
        Traits\WithdrawnDateField,
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
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="applications")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id")
     */
    protected $licence;

    /**
     * Has entered reg
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="has_entered_reg", nullable=true)
     */
    protected $hasEnteredReg;

    /**
     * Bankrupt
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="bankrupt", nullable=true)
     */
    protected $bankrupt;

    /**
     * Administration
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="administration", nullable=true)
     */
    protected $administration;

    /**
     * Disqualified
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="disqualified", nullable=true)
     */
    protected $disqualified;

    /**
     * Liquidation
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="liquidation", nullable=true)
     */
    protected $liquidation;

    /**
     * Receivership
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="receivership", nullable=true)
     */
    protected $receivership;

    /**
     * Insolvency confirmation
     *
     * @var unknown
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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="safety_confirmation", nullable=false)
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
     * Prev has licence
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="prev_has_licence", nullable=true)
     */
    protected $prevHasLicence;

    /**
     * Prev had licence
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="prev_had_licence", nullable=true)
     */
    protected $prevHadLicence;

    /**
     * Prev been disqualified eu
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="prev_been_disqualified_eu", nullable=true)
     */
    protected $prevBeenDisqualifiedEu;

    /**
     * Prev been revoked
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="prev_been_revoked", nullable=true)
     */
    protected $prevBeenRevoked;

    /**
     * Prev been at pi
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="prev_been_at_pi", nullable=true)
     */
    protected $prevBeenAtPi;

    /**
     * Prev been disqualified tc
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="prev_been_disqualified_tc", nullable=true)
     */
    protected $prevBeenDisqualifiedTc;

    /**
     * Prev purchased assets
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="prev_purchased_assets", nullable=true)
     */
    protected $prevPurchasedAssets;

    /**
     * Override ooo
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="override_ooo", nullable=false)
     */
    protected $overrideOoo = 0;

    /**
     * Prev conviction
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="prev_conviction", nullable=false)
     */
    protected $prevConviction = 0;

    /**
     * Convictions confirmation
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="convictions_confirmation", nullable=false)
     */
    protected $convictionsConfirmation = 0;

    /**
     * Psv operate small vhl
     *
     * @var unknown
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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="psv_small_vhl_confirmation", nullable=false)
     */
    protected $psvSmallVhlConfirmation = 0;

    /**
     * Psv no small vhl confirmation
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="psv_no_small_vhl_confirmation", nullable=false)
     */
    protected $psvNoSmallVhlConfirmation = 0;

    /**
     * Psv limosines
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="psv_limosines", nullable=false)
     */
    protected $psvLimosines = 0;

    /**
     * Psv no limosine confirmation
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="psv_no_limosine_confirmation", nullable=false)
     */
    protected $psvNoLimosineConfirmation = 0;

    /**
     * Psv only limosines confirmation
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="psv_only_limosines_confirmation", nullable=false)
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
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="application")
     */
    protected $documents;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->documents = new ArrayCollection();
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
     * Set the has entered reg
     *
     * @param unknown $hasEnteredReg
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
     * @return unknown
     */
    public function getHasEnteredReg()
    {
        return $this->hasEnteredReg;
    }


    /**
     * Set the bankrupt
     *
     * @param unknown $bankrupt
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
     * @return unknown
     */
    public function getBankrupt()
    {
        return $this->bankrupt;
    }


    /**
     * Set the administration
     *
     * @param unknown $administration
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
     * @return unknown
     */
    public function getAdministration()
    {
        return $this->administration;
    }


    /**
     * Set the disqualified
     *
     * @param unknown $disqualified
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
     * @return unknown
     */
    public function getDisqualified()
    {
        return $this->disqualified;
    }


    /**
     * Set the liquidation
     *
     * @param unknown $liquidation
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
     * @return unknown
     */
    public function getLiquidation()
    {
        return $this->liquidation;
    }


    /**
     * Set the receivership
     *
     * @param unknown $receivership
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
     * @return unknown
     */
    public function getReceivership()
    {
        return $this->receivership;
    }


    /**
     * Set the insolvency confirmation
     *
     * @param unknown $insolvencyConfirmation
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
     * @return unknown
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
     * @param unknown $safetyConfirmation
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
     * @return unknown
     */
    public function getSafetyConfirmation()
    {
        return $this->safetyConfirmation;
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
     * Set the prev has licence
     *
     * @param unknown $prevHasLicence
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
     * @return unknown
     */
    public function getPrevHasLicence()
    {
        return $this->prevHasLicence;
    }


    /**
     * Set the prev had licence
     *
     * @param unknown $prevHadLicence
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
     * @return unknown
     */
    public function getPrevHadLicence()
    {
        return $this->prevHadLicence;
    }


    /**
     * Set the prev been disqualified eu
     *
     * @param unknown $prevBeenDisqualifiedEu
     * @return Application
     */
    public function setPrevBeenDisqualifiedEu($prevBeenDisqualifiedEu)
    {
        $this->prevBeenDisqualifiedEu = $prevBeenDisqualifiedEu;

        return $this;
    }

    /**
     * Get the prev been disqualified eu
     *
     * @return unknown
     */
    public function getPrevBeenDisqualifiedEu()
    {
        return $this->prevBeenDisqualifiedEu;
    }


    /**
     * Set the prev been revoked
     *
     * @param unknown $prevBeenRevoked
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
     * @return unknown
     */
    public function getPrevBeenRevoked()
    {
        return $this->prevBeenRevoked;
    }


    /**
     * Set the prev been at pi
     *
     * @param unknown $prevBeenAtPi
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
     * @return unknown
     */
    public function getPrevBeenAtPi()
    {
        return $this->prevBeenAtPi;
    }


    /**
     * Set the prev been disqualified tc
     *
     * @param unknown $prevBeenDisqualifiedTc
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
     * @return unknown
     */
    public function getPrevBeenDisqualifiedTc()
    {
        return $this->prevBeenDisqualifiedTc;
    }


    /**
     * Set the prev purchased assets
     *
     * @param unknown $prevPurchasedAssets
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
     * @return unknown
     */
    public function getPrevPurchasedAssets()
    {
        return $this->prevPurchasedAssets;
    }


    /**
     * Set the override ooo
     *
     * @param unknown $overrideOoo
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
     * @return unknown
     */
    public function getOverrideOoo()
    {
        return $this->overrideOoo;
    }


    /**
     * Set the prev conviction
     *
     * @param unknown $prevConviction
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
     * @return unknown
     */
    public function getPrevConviction()
    {
        return $this->prevConviction;
    }


    /**
     * Set the convictions confirmation
     *
     * @param unknown $convictionsConfirmation
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
     * @return unknown
     */
    public function getConvictionsConfirmation()
    {
        return $this->convictionsConfirmation;
    }


    /**
     * Set the psv operate small vhl
     *
     * @param unknown $psvOperateSmallVhl
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
     * @return unknown
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
     * @param unknown $psvSmallVhlConfirmation
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
     * @return unknown
     */
    public function getPsvSmallVhlConfirmation()
    {
        return $this->psvSmallVhlConfirmation;
    }


    /**
     * Set the psv no small vhl confirmation
     *
     * @param unknown $psvNoSmallVhlConfirmation
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
     * @return unknown
     */
    public function getPsvNoSmallVhlConfirmation()
    {
        return $this->psvNoSmallVhlConfirmation;
    }


    /**
     * Set the psv limosines
     *
     * @param unknown $psvLimosines
     * @return Application
     */
    public function setPsvLimosines($psvLimosines)
    {
        $this->psvLimosines = $psvLimosines;

        return $this;
    }

    /**
     * Get the psv limosines
     *
     * @return unknown
     */
    public function getPsvLimosines()
    {
        return $this->psvLimosines;
    }


    /**
     * Set the psv no limosine confirmation
     *
     * @param unknown $psvNoLimosineConfirmation
     * @return Application
     */
    public function setPsvNoLimosineConfirmation($psvNoLimosineConfirmation)
    {
        $this->psvNoLimosineConfirmation = $psvNoLimosineConfirmation;

        return $this;
    }

    /**
     * Get the psv no limosine confirmation
     *
     * @return unknown
     */
    public function getPsvNoLimosineConfirmation()
    {
        return $this->psvNoLimosineConfirmation;
    }


    /**
     * Set the psv only limosines confirmation
     *
     * @param unknown $psvOnlyLimosinesConfirmation
     * @return Application
     */
    public function setPsvOnlyLimosinesConfirmation($psvOnlyLimosinesConfirmation)
    {
        $this->psvOnlyLimosinesConfirmation = $psvOnlyLimosinesConfirmation;

        return $this;
    }

    /**
     * Get the psv only limosines confirmation
     *
     * @return unknown
     */
    public function getPsvOnlyLimosinesConfirmation()
    {
        return $this->psvOnlyLimosinesConfirmation;
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
            $this->documents->remove($documents);
        }

        return $this;
    }

}
